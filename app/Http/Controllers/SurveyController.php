<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use App\Responses\Response;
use Auth;

use App\User as EMT;
use App\User;
use App\Http\ORM\Survey;
use App\Http\ORM\SurveyFields;
use App\Http\ORM\SurveyValues;
use App\Http\ORM\Urgence;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\ambulance as Ambulance;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

use Excel;

class SurveyController extends Controller
{
    public function user_surveys()
    {
        try {
            $PageTitle = "User Surveys";
            $user = Auth::user()->id;
            $surveys = Survey::where("is_deleted", 0)
                ->where("is_filled", 0)
                ->where("user_id", $user)
                ->get();
            foreach ($surveys as $survey) {
                $survey->driver_name = EMT::where(
                    "id",
                    MissionDriver::where(
                        "mission_id",
                        $survey->mission_id
                    )->first()->driver_id
                )->first()->nickname;
            }
            return view("user_survey", [
                "PageTitle" => $PageTitle,
                "surveys" => $surveys,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return view("errors.404");
        }
    }

    public function monthly_negative_comment()
    {
        if (
            !Auth::check() || (!Auth::user()->memberOf('Drivers Admin') && !Auth::user()->memberOf('Head of Station'))
        ) {
            return view("errors.404");
        }
        $page_title = "Generate Monthly Negative Report";
        $report_url = "monthly_negative_comment";
        return view(
            "generate_monthly_comments",
            compact("page_title", "report_url")
        );
    }

    public function monthly_negative_comment_report()
    {
        if (
            !Auth::check() || (!Auth::user()->memberOf('Drivers Admin') && !Auth::user()->memberOf('Head of Station'))
        ) {
            return view("errors.404");
        }

        $s = json_decode(Request::get("form"));

        $allSurveys = Survey::where("is_deleted", "0")
            ->where("is_filled", "1")
            ->whereYear("created_at", "=", $s->year)
            ->whereMonth("created_at", "=", $s->month)
            ->orderBy("mission_id")
            ->get();

        $data = [];
        $countt = SurveyFields::where("is_deleted", 0)
            ->where("type", "Bool")
            ->count();

        foreach ($allSurveys as $survey) {
            $positiveCount = $countt;
            $str = "";
            $values = SurveyValues::where("survey_id", $survey->id)
                ->whereIn(
                    "field_id",
                    SurveyFields::where("is_deleted", 0)
                        ->where("type", "Bool")
                        ->pluck("id")
                )
                ->get();
            foreach ($values as $value) {
                $f = SurveyFields::where("id", $value->field_id)->first();
                if ($value->value != $f->default) {
                    $positiveCount -= 1;
                    $str .= $f->arabic_description . " && ";
                }
            }
            if ($positiveCount != $countt) {
                $row = [];
                $driver_id = MissionDriver::where(
                    "mission_id",
                    $survey->mission_id
                )->first();
                if ($driver_id == null) {
                    continue;
                }
                $driver = User::where("id", $driver_id->driver_id)->first();
                if ($driver == null) {
                    continue;
                }
                $com = SurveyValues::where("survey_id", $survey->id)
                    ->where("field_id", 13)
                    ->first();
                if (
                    $com != null &&
                    $com->value != null &&
                    $com->value != "NULL"
                ) {
                    $comm = $com->value;
                } else {
                    $comm = "";
                }
                $row[] = $driver->nickname;
                $row[] = number_format(
                    (float) ($positiveCount / $countt),
                    2,
                    ".",
                    ","
                );
                $row[] = $str;
                $row[] = $comm;
                $row[] = $survey->created_at->format("d-m-Y");
                $data[] = $row;
            }
        }

        Excel::create("Monthly Negative Surveys", function ($excel) use (
            $data
        ) {
            // Set the title
            $excel->setTitle("Monthly Negative Surveys");
            // Chain the setters
            $excel->setCreator("502")->setCompany("LRC");

            $excel->sheet("Sheet 1", function ($sheet) use ($data) {
                $count = count($data);

                $sheet->fromArray($data, null, "A1");

                $sheet->setStyle([
                    "font" => [
                        "name" => "Calibri",
                        "size" => 10,
                        "bold" => false,
                    ],
                ]);

                $str = "A1:E" . ($count + 1);
                $sheet
                    ->getStyle($str)
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->setCellValue("A1", "Driver");
                $sheet->setCellValue("B1", "Score");
                $sheet->setCellValue("C1", "Wrong Sections");
                $sheet->setCellValue("D1", "Comment");
                $sheet->setCellValue("E1", "Urgence Date");

                $sheet
                    ->getStyle("A1:E1")
                    ->getFont()
                    ->setBold(true);
            });
        })->download("xlsx");

        return $data;
    }

    public function monthly_positive_comment()
    {
        if (
            !Auth::check() || (!Auth::user()->memberOf('Drivers Admin') && !Auth::user()->memberOf('Head of Station'))
        ) {
            return view("errors.404");
        }

        $page_title = "Generate Monthly Positive Report";
        $report_url = "monthly_positive_comment";
        return view(
            "generate_monthly_comments",
            compact("page_title", "report_url")
        );
    }

    public function monthly_positive_comment_report()
    {
        if (
            !Auth::check() || (!Auth::user()->memberOf('Drivers Admin') && !Auth::user()->memberOf('Head of Station'))
        ) {
            return view("errors.404");
        }

        $s = json_decode(Request::get("form"));

        $allSurveys = Survey::where("is_deleted", "0")
            ->where("is_filled", "1")
            ->whereYear("created_at", "=", $s->year)
            ->whereMonth("created_at", "=", $s->month)
            ->orderBy("mission_id")
            ->get();

        $data = [];
        $countt = SurveyFields::where("is_deleted", 0)
            ->where("type", "Bool")
            ->count();

        foreach ($allSurveys as $survey) {
            $positiveCount = $countt;
            $values = SurveyValues::where("survey_id", $survey->id)
                ->whereIn(
                    "field_id",
                    SurveyFields::where("is_deleted", 0)
                        ->where("type", "Bool")
                        ->pluck("id")
                )
                ->get();
            foreach ($values as $value) {
                $f = SurveyFields::where("id", $value->field_id)->first();
                if ($value->value != $f->default) {
                    $positiveCount -= 1;
                }
            }
            if ($positiveCount == $countt) {
                $row = [];
                $driver_id = MissionDriver::where(
                    "mission_id",
                    $survey->mission_id
                )->first();
                if ($driver_id == null) {
                    continue;
                }
                $driver = User::where("id", $driver_id->driver_id)->first();
                if ($driver == null) {
                    continue;
                }
                $com = SurveyValues::where("survey_id", $survey->id)
                    ->where("field_id", 13)
                    ->first();
                if (
                    $com != null &&
                    $com->value != null &&
                    $com->value != "NULL"
                ) {
                    $comm = $com->value;
                } else {
                    $comm = "";
                }
                $row[] = $driver->nickname;
                $row[] = number_format(
                    (float) ($positiveCount / $countt),
                    2,
                    ".",
                    ","
                );
                $row[] = $comm;
                $row[] = $survey->created_at->format("d-m-Y");
                $data[] = $row;
            }
        }

        Excel::create("Monthly Positive Surveys", function ($excel) use (
            $data
        ) {
            // Set the title
            $excel->setTitle("Monthly Positive Surveys");
            // Chain the setters
            $excel->setCreator("502")->setCompany("LRC");

            $excel->sheet("Sheet 1", function ($sheet) use ($data) {
                $count = count($data);

                $sheet->fromArray($data, null, "A1");

                $sheet->setStyle([
                    "font" => [
                        "name" => "Calibri",
                        "size" => 10,
                        "bold" => false,
                    ],
                ]);

                $str = "A1:D" . ($count + 1);
                $sheet
                    ->getStyle($str)
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->setCellValue("A1", "Driver");
                $sheet->setCellValue("B1", "Score");
                $sheet->setCellValue("C1", "Comment");
                $sheet->setCellValue("D1", "Urgence Date");

                $sheet
                    ->getStyle("A1:D1")
                    ->getFont()
                    ->setBold(true);
            });
        })->download("xlsx");

        return $data;
    }

    public function unfilled_user_surveys()
    {
        if ($this->val()) {
            try {
                $PageTitle = "Unfilled User Surveys";

                $data = Survey::where("is_deleted", 0)
                    ->where("is_filled", 0)
                    ->groupBy("user_id")
                    ->select("user_id", \DB::raw("count(1) as count"))
                    ->orderBy("count", "desc")
                    ->get();

                foreach ($data as $d) {
                    $user = User::where("id", $d->user_id)->first();
                    $d->name = $user->name;
                    $d->nickname = $user->nickname;
                }
                return view("survey_unfilled", [
                    "PageTitle" => $PageTitle,
                    "users" => $data,
                ]);
            } catch (\Exception $e) {
                Log::error($e);
                return view("errors.404");
            }
        } else {
            return view("errors.404");
        }
    }

    public function unfilledSurveyRatioReport()
    {
        if (!Auth::check() || !Auth::user()->memberOf("Drivers Admin")) {
            return view("errors.404");
        }

        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");

        if (!$this->verfiyDate($startDate) || !$this->verfiyDate($endDate)) {
            return "Invalid report Date";
        }

        $data = Survey::whereBetween("created_at", [
            $this->transform($startDate) . " 00:00:00",
            $this->transform($endDate) . " 23:59:59",
        ])
            ->where("sent", "=", 1)
            ->selectRaw(
                "SUM(1) as totalSurveys, SUM(is_filled=1) filledSurveys, SUM(is_filled=0) unfilledSurveys, SUM(is_filled=0)/SUM(1) ratio, user_id"
            )
            ->groupBy("user_id")
            ->orderBy("ratio", "DESC")
            ->orderBy("totalSurveys", "DESC")
            ->get();

        Excel::create("Unfilled Survey Ratios", function ($excel) use ($data) {
            // Set the title
            $excel->setTitle("Unfilled Survey Ratios");
            // Chain the setters
            $excel->setCreator("502")->setCompany("LRC");

            $excel->sheet("Unfilled Survey Ratios", function ($sheet) use (
                $data
            ) {
                $sheet->row(1, [
                    "EMT",
                    "Total Survey Count",
                    "Filled Survey Count",
                    "Unfilled Survey Count",
                    "Unfilled Survey Percetage",
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
                $sheet->cells("A2:E" . (count($data) + 1), function ($cells) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:E" . (count($data) + 1), "thick");

                $pos = 2;
                foreach ($data as $row) {
                    $sheet->row($pos, [
                        $row->user()->first()->name,
                        $row->totalSurveys,
                        $row->filledSurveys,
                        $row->unfilledSurveys,
                        ($row->ratio * 100) . " %"
                    ]);
                    $pos += 1;
                }
            });
        })->download("xlsx");
    }

    public function resetUnfilledSurveys()
    {
        if (!$this->val()) {
            $response = new Response();
            $response->Status = 0;
            $response->Message = 'Unauthorized Access';
            return json_encode($response);
        }

        try {
            $startDate = Request::get("startDate");
            $endDate = Request::get("endDate");



            if (!$this->verfiyDate($startDate) || !$this->verfiyDate($endDate)) {
                $response = new Response();
                $response->Status = 0;
                $response->Message = 'Invalid Date Range';
                return json_encode($response);
            }

            Survey::whereBetween("created_at", [
                $this->transform($startDate) . " 00:00:00",
                $this->transform($endDate) . " 23:59:59",
            ])->where('is_filled', false)
                ->update([
                    'is_filled' => true,
                    'filled_at' => date("Y-m-d H:i:s")
                ]);

            $response = new Response();
            $response->Status = 1;
            return json_encode($response);
        } catch (\Exception $e) {
            Log::error($e);
            $response = new Response();
            $response->Status = 0;
            $response->Message = "Failed Resetting Unfilled surveys";
            return json_encode($response);
        }
    }

    public function score($surveyId)
    {
        $count = SurveyFields::where("is_deleted", 0)
            ->where("type", "Bool")
            ->count();
        $positiveCount = 0;

        $values = SurveyValues::where("survey_id", $surveyId)
            ->whereIn(
                "field_id",
                SurveyFields::where("is_deleted", 0)
                    ->where("type", "Bool")
                    ->pluck("id")
            )
            ->get();
        foreach ($values as $value) {
            if (
                $value->value ==
                SurveyFields::where("id", $value->field_id)->first()->default
            ) {
                $positiveCount += 1;
            }
        }
        //return $positiveCount . " out of " . $count;
        return round(($positiveCount / $count) * 100) . " %";
    }

    public function hasComments($surveyId)
    {
        return SurveyValues::where("survey_id", $surveyId)
            ->where("comment", "!=", "")
            ->count();
    }

    public function view()
    {
        if ($this->val()) {
            $ambulances = Ambulance::select("id", "car_number")
                ->where("is_deleted", 0)
                ->get();
            $drivers = EMT::select("id", "nickname")
                ->where("driver", 1)
                ->where("is_deleted", 0)
                ->get();
            return view("survey", [
                "PageTitle" => "Survey Statistics",
                "drivers" => $drivers,
                "ambulances" => $ambulances,
            ]);
        } else {
            return view("errors.404");
        }
    }

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function val()
    {
        return Auth::check() && Auth::user()->memberOf("Drivers Admin");
    }

    private function verfiyDate($date)
    {
        if ($date == "") {
            return false;
        }
        try {
            list($mth, $day, $yr) = explode("/", $date);
            return checkdate($mth, $day, $yr);
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    private function transform($date)
    {
        list($mth, $day, $yr) = explode("/", $date);
        return $yr . "-" . $mth . "-" . $day;
    }

    public function search_survey()
    {
        if ($this->val()) {
            try {
                $s = json_decode(Request::get("form"));

                //We support search by driver, amulance, date frame
                if (isset($s->include_driver)) {
                    $driver = $s->driver == -1 ? "" : $s->driver;
                } else {
                    $driver = "";
                }

                if (isset($s->include_ambulance)) {
                    $ambulance = $s->ambulance == -1 ? "" : $s->ambulance;
                } else {
                    $ambulance = "";
                }

                if (isset($s->include_filled)) {
                    $filled = 1;
                } else {
                    $filled = 0;
                }

                $startDate = trim(explode("-", $s->reservation)[0]);
                $endDate = trim(explode("-", $s->reservation)[1]);

                $search = Survey::select(
                    "user_id",
                    "is_filled",
                    "created_at",
                    "filled_at",
                    "mission_id",
                    "guid",
                    "id"
                );

                if (
                    $this->verfiyDate($startDate) &&
                    $this->verfiyDate($endDate)
                ) {
                    $search->whereBetween("created_at", [
                        $this->transform($startDate) . " 00:00:00",
                        $this->transform($endDate) . " 23:59:59",
                    ]);
                }

                if ($driver != "") {
                    $search->whereIn(
                        "mission_id",
                        MissionDriver::where("driver_id", $driver)->pluck(
                            "mission_id"
                        )
                    );
                }

                if ($ambulance != "") {
                    $search->whereIn(
                        "mission_id",
                        Urgence::where("ambulance_id", $ambulance)->pluck("id")
                    );
                }

                $search->where("is_filled", $filled);

                $results = $search->where("is_deleted", 0)->get();

                foreach ($results as $result) {
                    if (
                        Auth::user()
                        ->groups()
                        ->get()
                        ->contains(
                            "group_id",
                            \App\Http\ORM\Group::where(
                                "description",
                                "Head of Station"
                            )->first()->id
                        ) ||
                        !$result->is_filled
                    ) {
                        $result->user = User::where(
                            "id",
                            $result->user_id
                        )->first()->nickname;
                    } else {
                        $result->user = "Hidden";
                    }

                    $result->driver = User::where(
                        "id",
                        MissionDriver::where(
                            "mission_id",
                            $result->mission_id
                        )->first()->driver_id
                    )->first()->nickname;
                    $result->ambulance = Ambulance::where(
                        "id",
                        Urgence::where("id", $result->mission_id)->first()
                            ->ambulance_id
                    )->first()->car_number;
                    $result->patient = str_replace(
                        "%%%",
                        "",
                        Urgence::where("id", $result->mission_id)->first()
                            ->patient_name
                    );
                    $result->created = explode(" ", $result->created_at)[0];
                    $result->date = explode(
                        " ",
                        Urgence::where("id", $result->mission_id)->first()
                            ->dep_to_site
                    )[0];
                    $result->filled_at = $result->is_filled
                        ? explode(" ", $result->filled_at)[0]
                        : "Not yet";
                    $result->score = $result->is_filled
                        ? $this->score($result->id)
                        : "Not Filled";
                    $result->comments = $result->is_filled
                        ? $this->hasComments($result->id)
                        : "Not Filled";
                    //$values = SurveyValues::where('survey_id',$result->id)->where('get()

                    //$result->score = $values
                }
                $response = new Response();
                $response->Status = 1;
                $response->Data = $results;
                return json_encode($response);
            } catch (\Exception $e) {
                Log::error($e);
                $response = new Response();
                $response->Status = 0;
                return json_encode($response);
            }
        } else {
            $response = new Response();
            $response->Status = 0;
            return json_encode($response);
        }
    }

    public function investigate()
    {
        if ($this->val()) {
            $surveyId = Input::get("Id");
            if ($surveyId == "") {
                return view("errors.404");
            }

            $survey = Survey::where("id", $surveyId)->first();

            if (is_null($survey)) {
                return view("errors.404");
            }

            $fields = SurveyFields::select(
                "id",
                "id as name",
                "arabic_description",
                "type",
                "default"
            )
                ->where("is_deleted", 0)
                ->orderBy("order")
                ->get();

            foreach ($fields as $field) {
                $value = SurveyValues::where("survey_id", $survey->id)
                    ->where("field_id", $field->id)
                    ->first();
                if (is_null($value)) {
                    if ($field->type == "Bool") {
                        $field->value = $field->default;
                        $field->comment = "";
                    } elseif ($field->type == "Text") {
                        $field->value = "";
                    }
                } else {
                    $field->value = $value->value;
                    $field->comment = $value->comment;
                }
            }

            $emergency = Urgence::where("id", $survey->mission_id)->first();
            $driver_id = $emergency->driver()->first()->driver_id;
            $driver = User::where("id", $driver_id)->first();

            if (
                Auth::user()
                ->groups()
                ->get()
                ->contains(
                    "group_id",
                    \App\Http\ORM\Group::where(
                        "description",
                        "Head of Station"
                    )->first()->id
                )
            ) {
                $emt = User::where("id", $survey->user_id)->first();
            } else {
                $user = new User();
                $user->nickname = "Hidden";
                $emt = $user;
            }
            $ambulance = Ambulance::where(
                "id",
                $emergency->ambulance_id
            )->first()->car_number;
            $departure = $emergency->dep_to_site;
            $patient = str_replace("%%%", "", $emergency->patient_name);

            return view("view_survey", [
                "emt" => $emt,
                "fields" => $fields,
                "driver" => $driver,
                "guid" => $surveyId,
                "ambulance" => $ambulance,
                "departure" => $departure,
                "patient" => $patient,
            ]);
        } else {
            return view("errors.404");
        }
    }
}
