<?php

namespace App\Http\Controllers;

use App\Http\ORM\UserPoint;
use Request;
use Auth;
use Excel;
use Illuminate\Support\Facades\Input;

use App\Responses\Response;
use App\Http\ORM\PointAction;
use App\Http\ORM\Event;
use App\User;

use Illuminate\Support\Facades\Log;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Same groups the sidebar uses to show "Manage EMT's Points"
    private function canManagePoints()
    {
        return Auth::user()->memberOf('Points Admin') ||
            Auth::user()->memberOf('Chef Equipe');
    }

    // Same groups the sidebar uses to show "Manage Point System"
    private function canManageActions()
    {
        return Auth::user()->memberOf('Points Admin') ||
            Auth::user()->memberOf('Secretariat Admin');
    }

    private function denied()
    {
        $response = array();
        $response['Result'] = "ERROR";
        $response['Message'] = "Unauthorized access";
        return json_encode($response);
    }

    public function points()
    {
        $page_title = "Point System Grid";
        $point_actions = PointAction::get();
        return view('points_grid', ['page_title' => $page_title, 'point_actions' => $point_actions]);
    }

    public function manage()
    {
        if (!$this->canManagePoints()) {
            return view('errors.404');
        }

        $page_title = "Manage EMT's Points";
        $users = User::get();
        $points = PointAction::select(['id as Value', 'name as DisplayText'])->get();
        return view('manage_points', ['page_title' => $page_title, 'users' => $users, 'points' => $points]);
    }

    public function manage_actions()
    {
        if (!$this->canManageActions()) {
            return view('errors.404');
        }

        $page_title = "Manage Point System";
        return view('manage_point_actions', ['page_title' => $page_title]);
    }

    public function view()
    {
        $page_title = "View My Points";
        $points = PointAction::select(['id as Value', 'name as DisplayText'])->get();
        return view('view_points', ['page_title' => $page_title, 'points' => $points]);
    }

    public function user_points()
    {

        try {

            if (Request::has("userId")) {
                $userId = Request::get("userId");

                // Everyone may read their own points; other users' only if they manage points
                if ($userId != Auth::user()->id && !$this->canManagePoints()) {
                    return $this->denied();
                }
            } else {
                $userId = Auth::user()->id;
            }
            $year = Request::get("year");

            $points = $this->get_user_points($userId, $year);

            $response = array();
            $response['Result'] = "OK";
            $response['Records'] = $points;
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "OK";
            $response['Message'] = "Failed Getting User Points";
            return json_encode($response);
        }
    }

    public function all_points()
    {

        try {
            $response = array();
            $response['Result'] = "OK";
            $response['Options'] = PointAction::select('id as Value')->select('name as DisplayText')->get();
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response["Result"] = "ERROR";
            $response["Message"] = "Failed Getting Points";
            return json_encode($response);
        }
    }

    private function get_user_points($user, $year)
    {

        return UserPoint::where('user_id', $user)
            ->whereYear('application_date', $year)
            ->with('pointAction')
            ->orderBy('application_date', 'asc')
            ->get();
    }

    public function update_point()
    {
        if (!$this->canManagePoints()) {
            return $this->denied();
        }

        try {

            $id = Request::get("id");
            $application_date = Request::get("application_date");
            $application_notes = Request::get("application_notes");

            \DB::transaction(function () use ($id, $application_date, $application_notes) {

                $point = UserPoint::where('id', $id)->with('pointAction')->first();
                if (is_null($point))
                    throw new \Exception('Wrong User Point ID while editing, ID: ' + $id + '.User: ' + Auth::user()->nickname);

                if (isset($application_notes)) {

                    if ($application_notes != $point->application_notes) {
                        $ev = new Event();
                        $ev->create('Edit User Point', "Change note from $point->application_notes to $application_notes");
                        $ev->save();
                        $point->application_notes = $application_notes;
                        $point->save();
                    }
                }

                if (isset($application_date)) {

                    if ($application_date != $point->application_date) {
                        $ev = new Event();
                        $ev->create('Edit User Point', "Change date from [{$point->application_date}] to [{$application_date}]");
                        $ev->save();
                        $point->application_date = $application_date;
                        $point->save();
                    }
                }
            });

            $response['Result'] = "OK";
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "ERROR";
            $response['Message'] = "Failed Saving Details";
            return json_encode($response);
        }
    }

    public function add_point()
    {
        if (!$this->canManagePoints()) {
            return $this->denied();
        }

        try {

            $application_date = Request::get("application_date");
            $application_notes = Request::get("application_notes");
            $user_id = Request::get("user_id");
            $point_action_id = Request::get("point_action_id");

            $record = \DB::transaction(function () use ($user_id, $point_action_id, $application_date, $application_notes) {

                $user_point = new UserPoint();
                $user_point->user_id = $user_id;
                $user_point->point_action_id = $point_action_id;
                $user_point->application_date = $application_date;
                $user_point->application_notes = $application_notes;
                $user_point->save();

                $ev = new Event();
                $ev->create('Insert User Point', '');
                $ev->save();

                return $user_point;
            });

            $record->point_action = PointAction::where('id', $record->point_action_id)->first();
            $response['Result'] = "OK";
            $response['Record'] = $record;
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "ERROR";
            $response['Message'] = "Failed Saving Details";
            return json_encode($response);
        }
    }

    public function delete_point()
    {
        if (!$this->canManagePoints()) {
            return $this->denied();
        }

        try {

            $id = Request::get("id");

            \DB::transaction(function () use ($id) {

                $user_point = UserPoint::where('id', $id)->first();

                $ev = new Event();
                $ev->create('Remove User Point', "Remove User Point [{$user_point}]");
                $ev->save();

                $user_point->delete();
            });

            $response['Result'] = "OK";
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "ERROR";
            $response['Message'] = "Failed Saving Details";
            return json_encode($response);
        }
    }

    public function list_actions()
    {

        try {

            $points = PointAction::get();
            $response = array();
            $response['Result'] = "OK";
            $response['Records'] = $points;
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "OK";
            $response['Message'] = "Failed Getting User Points";
            return json_encode($response);
        }
    }

    public function update_action()
    {
        if (!$this->canManageActions()) {
            return $this->denied();
        }

        try {

            $id = Request::get("id");
            $name = Request::get("name");
            $points = Request::get("points");

            \DB::transaction(function () use ($id, $name, $points) {

                $point = PointAction::where('id', $id)->first();
                if (is_null($point))
                    throw new \Exception('Wrong System Point ID while editing, ID: ' + $id);

                if (isset($name)) {

                    if ($name != $point->name) {
                        $ev = new Event();
                        $ev->create('Edit Point', "Change name from [{$point->name}] to [{$name}]");
                        $ev->save();
                        $point->name = $name;
                        $point->save();
                    }
                }

                if (isset($points)) {

                    if (!is_numeric($points)) {
                        throw new \Exception('Points should be a number. Value ' + $points + ' not accepted.');
                    }

                    if ($points != $point->points) {
                        $ev = new Event();
                        $ev->create('Edit Point', "Change points from [{$point->points}] to [{$points}]");
                        $ev->save();
                        $point->points = $points;
                        $point->save();
                    }
                }
            });

            $response['Result'] = "OK";
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "ERROR";
            $response['Message'] = "Failed Saving Details";
            return json_encode($response);
        }
    }

    public function add_action()
    {
        if (!$this->canManageActions()) {
            return $this->denied();
        }

        try {

            $name = Request::get("name");
            $points = Request::get("points");

            if (!is_numeric($points)) {
                throw new \Exception('Points should be a number. Value ' + $points + ' not accepted.');
            }

            $record = \DB::transaction(function () use ($name, $points) {

                $point = new PointAction();
                $point->name = $name;
                $point->points = $points;
                $point->save();

                $ev = new Event();
                $ev->create('Insert Point', "");
                $ev->save();

                return $point;
            });

            $response['Result'] = "OK";
            $response['Record'] = $record;
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = array();
            $response['Result'] = "ERROR";
            $response['Message'] = "Failed Saving Details";
            return json_encode($response);
        }
    }

    public function point_report()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf('Points Admin') ||
            Auth::user()->memberOf('Chef Equipe');

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        $data = \DB::select('SELECT 
                    u.nickname nickname, 12 + COALESCE(SUM(pa.points), 0) points
                FROM
                    users u
                        LEFT JOIN
                    user_point up ON up.user_id = u.id
                        AND YEAR(up.application_date) = ' . $year . '
                        LEFT JOIN
                    point_action pa ON up.point_action_id = pa.id
                WHERE
                    u.is_deleted = 0
                GROUP BY u.nickname
                ORDER BY u.nickname');

        Excel::create("Point System Report " . $year, function (
            $excel
        ) use ($data, $year) {
            $excel
                ->setTitle("Point System Report for 502")
                ->setCreator("IT")
                ->setCompany("LRC 502")
                ->setDescription("Point System Report for LRC 502");

            $excel->sheet("Report" . $year, function ($sheet) use ($data) {
                $sheet->row(1, [
                    "EMT",
                    "Points"
                ]);

                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });

                $sheet->freezeFirstRow();
                $sheet->cells("A2:B" . (count($data) + 1), function (
                    $cells
                ) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);

                $pos = 2;
                foreach ($data as $user) {
                    $sheet->row($pos, [$user->nickname, $user->points]);
                    $pos += 1;
                }
            });
        })->download("xls");
    }
}
