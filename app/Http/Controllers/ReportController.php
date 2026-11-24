<?php
namespace App\Http\Controllers;

use Request;
use Response;
use DateTime;
use app\Http\Requests;
use Auth;
use Illuminate\Support\Facades\Input;
use Excel;

use App\User as EMT;
use App\Http\ORM\ambulance;
use App\Http\ORM\mission_type;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\hospital;
use App\Http\ORM\location as Village;
use App\Http\ORM\station;
use App\Http\ORM\Comment as comment;
use App\Http\ORM\Urgence;
use App\Http\ORM\MissionEMT;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\Nationalities as Nationality;
use App\Http\ORM\Survey;

use Illuminate\Support\Facades\Log;
class ReportController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    private function verfiyDate($date)
    {
        if ($date == "") {
            return false;
        }
        Log::warning($date);
        list($mth, $day, $yr) = explode("/", $date);
        return checkdate($mth, $day, $yr);
        //if (preg_match("'/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/'",$date)) {
        //    return true;
        //} else {
        //    return false;
        //}
    }

    public function view()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        //$secretariat_admin = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Secretariat Admin')->first()->id);
        //$head_of_station = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id);

        $access = Auth::user()->memberOf("Statistic");

        //if(!$secretariat_admin && !$head_of_station) return "Unauthorized access";
        if (!$access) {
            return "Unauthorized access";
        }

        $page_title = "Emergency Reports";
        return view("generate_emergency_report", ["page_title" => $page_title]);
    }

    private function transform($date)
    {
        list($mth, $day, $yr) = explode("/", $date);
        return $yr . "-" . $mth . "-" . $day;
    }

    private function setTo($urg)
    {
        try {
            $t = explode(".", $urg->to);
            if ($t[0] == 0) {
                $to = Hospital::where("id", $t[1])->first();
                if ($to != null) {
                    $urg->to = $to->name . " ( " . $to->code . " ) ";
                } else {
                    $urg->to = "N/A";
                }
            } elseif ($t[0] == 1 || $t[0] == 2) {
                $to = Village::where("id", $t[1])->first();
                if ($to != null) {
                    $urg->to = $to->village;
                } else {
                    $urg->to = "N/A";
                }
            } elseif ($t[0] == 3) {
                $urg->to = "C " . $t[1];
            } else {
                $urg->to = $urg->to_other;
            }
            return $urg;
        } catch (\Exception $e) {
            Log::error($e);
            $urg->to = "N/A";
            return $urg;
        }
    }

    public function createTimesReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        if (true) {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $data = Urgence::select(
                \DB::raw('type as t 
					,dep_to_site
					,`from`
					,`to`
					,timestampdiff(minute,dep_to_site,arr_on_site) as t1
					,timestampdiff(minute,arr_on_site,dep_from_site) as t2
					,timestampdiff(minute,dep_from_site,arr_to_dest) as t3
					,timestampdiff(minute,arr_to_dest,unit_available) as t4')
            )
                ->where("is_deleted", 0)
                ->whereMonth("dep_to_site", $month)
                ->whereYear("dep_to_site", $year)
                ->get();

            $array = [];

            $array[10] = [];
            $array[11] = [];

            $array[20] = [];
            $array[21] = [];

            $array[30] = [];
            $array[31] = [];

            foreach ($data as $d) {
                $date_type = 0;
                $mission_type = 0;

                $date = new \DateTime($d->dep_to_site);
                $day = $date->format("N");

                $start = 7;
                $end = 15;

                if ($day == 6) {
                    $end = 14;
                } elseif ($day == 7) {
                    $start = -1;
                    $end = -1;
                }

                $hour = $date->format("G");

                if ($hour >= $start && $hour <= $end) {
                    $date_type = 1;
                }

                if (in_array($d->t, [12, 20])) {
                    $mission_type = 1; //3ade
                } elseif (!in_array($d->t, [13, 14, 15, 16])) {
                    $mission_type = 2; //Urgence
                    if (
                        substr($d->from, 0, 1) == 0 &&
                        substr($d->to, 0, 1) == 0
                    ) {
                        $mission_type = 3;
                    } //H-H
                }

                $array[10 * $mission_type + $date_type][] = $d;
            }

            Excel::create("Times_" . $month . "_" . $year, function (
                $excel
            ) use ($array) {
                $excel
                    ->setTitle("Times report for 502")
                    ->setCreator("IT")
                    ->setCompany("LRC 502")
                    ->setDescription("!!");

                $excel->sheet("Times", function ($sheet) use ($array) {
                    $sheet->row(1, [
                        "",
                        "DTS --> ATS",
                        "ATS --> DFS",
                        "DFS --> ATD",
                        "ATD --> UR",
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
                    $sheet->cells("A2:E13", function ($cells) {
                        $cells->setAlignment("left");
                        $cells->setVAlignment("top");
                        $cells->setFontSize(14);
                    });
                    $sheet->setAutoSize(true);

                    $sheet->mergeCells("A2:E2");
                    $sheet->mergeCells("A6:E6");
                    $sheet->mergeCells("A10:E10");

                    $sheet->setBorder("A1:E13", "thick");

                    $sheet->row(2, ["عادي"]);
                    $sheet->row(6, ["متعددة"]);
                    $sheet->row(10, ["H - H"]);

                    for ($pos = 1; $pos <= 3; $pos++) {
                        $avrg = [];
                        for ($i = 0; $i < 12; $i++) {
                            $avrg[$i] = 0;
                        }
                        $count1 = 0;
                        $count2 = 0;
                        foreach ($array[10 * $pos] as $u) {
                            $avrg[0] += $u->t1;
                            $avrg[1] += $u->t2;
                            $avrg[2] += $u->t3;
                            $avrg[3] += $u->t4;

                            $avrg[8] += $u->t1;
                            $avrg[9] += $u->t2;
                            $avrg[10] += $u->t3;
                            $avrg[11] += $u->t4;
                            $count1 += 1;
                        }

                        foreach ($array[10 * $pos + 1] as $u) {
                            $avrg[4] += $u->t1;
                            $avrg[5] += $u->t2;
                            $avrg[6] += $u->t3;
                            $avrg[7] += $u->t4;

                            $avrg[8] += $u->t1;
                            $avrg[9] += $u->t2;
                            $avrg[10] += $u->t3;
                            $avrg[11] += $u->t4;
                            $count2 += 1;
                        }

                        if ($count1 + $count2 == 0) {
                            $sheet->row(($pos - 1) * 4 + 3, [
                                "Total",
                                "--",
                                "--",
                                "--",
                                "--",
                            ]);
                        } else {
                            $sheet->row(($pos - 1) * 4 + 3, [
                                "Total",
                                $avrg[8] / ($count1 + $count2),
                                $avrg[9] / ($count1 + $count2),
                                $avrg[10] / ($count1 + $count2),
                                $avrg[11] / ($count1 + $count2),
                            ]);
                        }

                        if ($count1 == 0) {
                            $sheet->row(($pos - 1) * 4 + 4, [
                                "Day",
                                "--",
                                "--",
                                "--",
                                "--",
                            ]);
                        } else {
                            $sheet->row(($pos - 1) * 4 + 4, [
                                "Day",
                                $avrg[0] / $count1,
                                $avrg[1] / $count1,
                                $avrg[2] / $count1,
                                $avrg[3] / $count1,
                            ]);
                        }

                        if ($count2 == 0) {
                            $sheet->row(($pos - 1) * 4 + 5, [
                                "Night",
                                "--",
                                "--",
                                "--",
                                "--",
                            ]);
                        } else {
                            $sheet->row(($pos - 1) * 4 + 5, [
                                "Night",
                                $avrg[4] / $count2,
                                $avrg[5] / $count2,
                                $avrg[6] / $count2,
                                $avrg[7] / $count2,
                            ]);
                        }
                    }
                });
            })->download("xls");
        } else {
            return;
        }
    }

    public function createVillageReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        if (true) {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $villages = \DB::select(
                'SELECT 
					L.village as V
					,count(1) as C
				FROM 
					urgence U
					JOIN
					location L 
						ON 
							SUBSTR(`from`,3) = L.id  
				WHERE 
					U.dep_to_site >= "' .
                    $year .
                    "-" .
                    $month .
                    '-1 00:00:00"
					AND U.dep_to_site <= "' .
                    $year .
                    "-" .
                    $month .
                    "-" .
                    $days .
                    ' 23:59:59"
					AND LEFT(`from`,1) IN (1,2) 
					AND SUBSTR(`from`,2) <> ""
					AND U.is_deleted = 0
				GROUP BY
					L.village;'
            );

            Excel::create("Village_" . $month . "_" . $year, function (
                $excel
            ) use ($villages) {
                $excel
                    ->setTitle("Village report for 502")
                    ->setCreator("IT")
                    ->setCompany("LRC 502")
                    ->setDescription("!!");

                $excel->sheet("Village", function ($sheet) use ($villages) {
                    $sheet->row(1, ["Village", "Count"]);

                    $sheet->row(1, function ($row) {
                        // call cell manipulation methods
                        $row->setBackground("#939FA3");
                        $row->setAlignment("center");
                        $row->setVAlignment("center");
                        $row->setFontSize(16);
                        $row->setFontWeight("bold");
                    });

                    $sheet->freezeFirstRow();
                    $sheet->cells("A2:B" . (count($villages) + 1), function (
                        $cells
                    ) {
                        $cells->setAlignment("left");
                        $cells->setVAlignment("top");
                        $cells->setFontSize(14);
                    });
                    $sheet->setAutoSize(true);
                    $sheet->setBorder("A1:B" . (count($villages) + 1), "thick");

                    $pos = 2;
                    foreach ($villages as $u) {
                        $sheet->row($pos, [$u->V, $u->C]);
                        $pos += 1;
                    }
                });
            })->download("xls");
        } else {
            return;
        }
    }

    public function createAmbulanceReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }
        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $query =
            "select amb.car_number car_number ,t.name mission_type,count(1) count " .
            "from (select ambulance_id,type,parent_id,count(1) from urgence where dep_to_site between " .
            '"' .
            $year .
            "-" .
            $month .
            '-01 00:00:00" and "' .
            $year .
            "-" .
            $month .
            "-" .
            $days .
            ' 23:59:59" and is_deleted = 0 ' .
            "group by ambulance_id,type,parent_id) a " .
            "join ambulance amb on amb.id = a.ambulance_id " .
            "join mission_type t on t.id = a.type " .
            "group by ambulance_id,type order by car_number, name";

        $data = \DB::select($query);

        Excel::create("Ambulance_" . $month . "_" . $year, function (
            $excel
        ) use ($data) {
            $excel
                ->setTitle("Ambulance report")
                ->setCreator("IT")
                ->setCompany("LRC 502")
                ->setDescription("!!");
            $excel->sheet("Ambulance", function ($sheet) use ($data) {
                $sheet->row(1, ["Ambulance", "Mission Type", "Count"]);
                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
                $sheet->freezeFirstRow();
                $sheet->cells("A2:C" . (count($data) + 1), function ($cells) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:C" . (count($data) + 1), "thick");
                $pos = 2;
                foreach ($data as $u) {
                    $sheet->row($pos, [
                        $u->car_number,
                        $u->mission_type,
                        $u->count,
                    ]);
                    $pos += 1;
                }
            });
        })->download("xls");
    }

    public function createDetailedReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }
        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        $query =
            "select mt.name Mission_Type,patient_age Age,patient_sex,call_time,dep_to_site,arr_on_site,dep_from_site,arr_to_dest,unit_available,ret_to_centre " .
            " from urgence u join mission_type mt on mt.id = u.type where patient_name is not null " .
            " and YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " AND u.IS_DELETED = 0";

        $data = \DB::select($query);

        Excel::create("Detailed_Report_" . $month . "_" . $year, function (
            $excel
        ) use ($data) {
            $excel
                ->setTitle("Detailed Mission Report")
                ->setCreator("IT")
                ->setCompany("LRC 502")
                ->setDescription("!!");
            $excel->sheet("Patient Info", function ($sheet) use ($data) {
                $sheet->row(1, [
                    "Mission Type",
                    "Patient Age",
                    "Patient Sex",
                    "Call Time",
                    "Departure to Site",
                    "Arrival on Site",
                    "Departure from Site",
                    "Arrival to Destination",
                    "Unit Avialable",
                    "Return to Centre",
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
                $sheet->cells("A2:J" . (count($data) + 3), function ($cells) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:J" . (count($data) + 3), "thick");
                $pos = 2;
                $total = 0;
                foreach ($data as $u) {
                    $sheet->row($pos, [
                        $u->Mission_Type,
                        $u->Age,
                        $u->patient_sex,
                        $u->call_time,
                        $u->dep_to_site,
                        $u->arr_on_site,
                        $u->dep_from_site,
                        $u->arr_to_dest,
                        $u->unit_available,
                        $u->ret_to_centre,
                    ]);
                    $pos += 1;
                }
            });
        })->download("xlsx");
    }

    public function createInfectousReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }
        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        $query =
            "select u.nickname User,count(1) Count from mission_driver md join users u " .
            "on md.driver_id = u.id where mission_id in " .
            "(SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " AND IS_DELETED = 0 AND is_day_shift(dep_to_site) = 1 ) group by User order by Count desc";

        $queryEmt =
            "select u.nickname User, count(1) Count from " .
            " (select mission_leader_id user_id from mission_mission_leader where " .
            "mission_id in (SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " and is_deleted = 0 AND is_day_shift(dep_to_site) = 1 ) " .
            " union all" .
            " select emt_id user_id  from mission_emts where " .
            " mission_id in (SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " and is_deleted = 0 AND is_day_shift(dep_to_site) = 1 ) ) " .
            " m join users u on u.id = m.user_id group by User order by Count desc";

        $query2 =
            "select u.nickname User,count(1) Count from mission_driver md join users u " .
            "on md.driver_id = u.id where mission_id in " .
            "(SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " AND IS_DELETED = 0 AND is_day_shift(dep_to_site) = 0 ) group by User order by Count desc";

        $queryEmt2 =
            "select u.nickname User, count(1) Count from " .
            " (select mission_leader_id user_id from mission_mission_leader where " .
            "mission_id in (SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " and is_deleted = 0 AND is_day_shift(dep_to_site) = 0 ) " .
            " union all" .
            " select emt_id user_id  from mission_emts where " .
            " mission_id in (SELECT ID FROM urgence WHERE TYPE=8 AND YEAR(DEP_TO_SITE) = " .
            $year .
            " AND MONTH(DEP_TO_SITE) = " .
            $month .
            " and is_deleted = 0 AND is_day_shift(dep_to_site) = 0 ) ) " .
            " m join users u on u.id = m.user_id group by User order by Count desc";
        $drivers = \DB::select($query);
        $emts = \DB::select($queryEmt);
        $drivers2 = \DB::select($query2);
        $emts2 = \DB::select($queryEmt2);

        Excel::create("Infecteous_Disease_" . $month . "_" . $year, function (
            $excel
        ) use ($drivers, $emts, $drivers2, $emts2) {
            $excel
                ->setTitle("Infecteous Disease Report")
                ->setCreator("IT")
                ->setCompany("LRC 502")
                ->setDescription("!!");
            $excel->sheet("Day Shift Drivers", function ($sheet) use (
                $drivers
            ) {
                $sheet->row(1, ["Driver Nickname", "Count"]);
                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
                $sheet->freezeFirstRow();
                $sheet->cells("A2:B" . (count($drivers) + 3), function (
                    $cells
                ) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . (count($drivers) + 3), "thick");
                $pos = 2;
                $total = 0;
                foreach ($drivers as $u) {
                    $sheet->row($pos, [$u->User, $u->Count]);
                    $total += $u->Count;
                    $pos += 1;
                }
                $sheet->row(count($drivers) + 2, ["Total : ", $total]);
                $sheet->row(count($drivers) + 2, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
            });
            $excel->sheet("Drivers", function ($sheet) use ($drivers2) {
                $sheet->row(1, ["Driver Nickname", "Count"]);
                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
                $sheet->freezeFirstRow();
                $sheet->cells("A2:B" . (count($drivers2) + 3), function (
                    $cells
                ) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . (count($drivers2) + 3), "thick");
                $pos = 2;
                $total = 0;
                foreach ($drivers2 as $u) {
                    $sheet->row($pos, [$u->User, $u->Count]);
                    $total += $u->Count;
                    $pos += 1;
                }
                $sheet->row(count($drivers2) + 2, ["Total : ", $total]);
                $sheet->row(count($drivers2) + 2, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
            });
            $excel->sheet("Day Shift EMTs", function ($sheet) use ($emts) {
                $sheet->row(1, ["EMT Nickname", "Count"]);
                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
                $sheet->freezeFirstRow();
                $sheet->cells("A2:B" . (count($emts) + 3), function ($cells) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . (count($emts) + 3), "thick");
                $pos = 2;
                $total = 0;
                foreach ($emts as $u) {
                    $sheet->row($pos, [$u->User, $u->Count]);
                    $total += $u->Count;
                    $pos += 1;
                }
                $sheet->row(count($emts) + 2, ["Total : ", $total]);
                $sheet->row(count($emts) + 2, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
            });
            $excel->sheet("EMTs", function ($sheet) use ($emts2) {
                $sheet->row(1, ["EMT Nickname", "Count"]);
                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
                $sheet->freezeFirstRow();
                $sheet->cells("A2:B" . (count($emts2) + 3), function ($cells) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . (count($emts2) + 3), "thick");
                $pos = 2;
                $total = 0;
                foreach ($emts2 as $u) {
                    $sheet->row($pos, [$u->User, $u->Count]);
                    $total += $u->Count;
                    $pos += 1;
                }
                $sheet->row(count($emts2) + 2, ["Total : ", $total]);
                $sheet->row(count($emts2) + 2, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });
            });
        })->download("xlsx");
    }

    public function createEMTSupportReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");

        if ($this->verfiyDate($startDate) && $this->verfiyDate($endDate)) {
            $types = \App\Http\ORM\Register::whereBetween("register_date", [
                $this->transform($startDate) . " 00:00:00",
                $this->transform($endDate) . " 23:59:59",
            ])
                ->where("badal", "!=", -1)
                ->leftJoin(
                    "shift_emt",
                    "shift_emt.shift_id",
                    "=",
                    "register.id"
                )
                ->leftJoin("users", "users.id", "=", "shift_emt.user_id")
                ->leftJoin(
                    "users as users2",
                    "users2.id",
                    "=",
                    "shift_emt.badal"
                )
                ->select(
                    "users.name as name1",
                    "users2.name as name2",
                    "register.register_date"
                )
                ->get();

            Excel::create("EMT_SUPPORT_REPORT", function ($excel) use ($types) {
                $excel
                    ->setTitle("EMT Support report for 502")
                    ->setCreator("IT")
                    ->setCompany("LRC 502")
                    ->setDescription("!!");

                $excel->sheet("Report", function ($sheet) use ($types) {
                    $sheet->row(1, ["EMT", "Badal", "Date"]);

                    $sheet->row(1, function ($row) {
                        // call cell manipulation methods
                        $row->setBackground("#939FA3");
                        $row->setAlignment("center");
                        $row->setVAlignment("center");
                        $row->setFontSize(16);
                        $row->setFontWeight("bold");
                    });

                    $sheet->freezeFirstRow();
                    $sheet->cells("A2:C" . (count($types) + 1), function (
                        $cells
                    ) {
                        $cells->setAlignment("left");
                        $cells->setVAlignment("top");
                        $cells->setFontSize(14);
                    });
                    $sheet->setAutoSize(true);
                    $sheet->setBorder("A1:C" . (count($types) + 1), "thick");

                    $pos = 2;
                    foreach ($types as $u) {
                        $sheet->row($pos, $u->toArray());
                        $pos += 1;
                    }
                });
            })->download("xls");
        } else {
            return;
        }
    }

    public function roleReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");

        if (!$this->verfiyDate($startDate) && !$this->verfiyDate($endDate)) {
            return "Invalid Date Range";
        }

        $query = "SELECT                                            \n" .
        "    u.name name,\n" .
        "    q.role role,\n" .
        "    SUM(1) count\n" .
        "FROM (SELECT\n" .
        "        md.driver_id user_id,\n" .
        "        'Driver' role\n" .
        "    FROM urgence\n" .
        "        JOIN mission_driver md\n" .
        "        ON md.mission_id = urgence.id\n" .
        "    WHERE is_deleted = 0\n" .
        "    AND dep_to_site BETWEEN ? AND ?\n" .
        "    UNION ALL\n" .
        "    SELECT\n" .
        "        ml.mission_leader_id user_id,\n" .
        "        'Mission Leader' role\n" .
        "    FROM urgence\n" .
        "        JOIN mission_mission_leader ml\n" .
        "        ON ml.mission_id = urgence.id\n" .
        "    WHERE is_deleted = 0\n" .
        "    AND dep_to_site BETWEEN ? AND ?\n" .
        "    UNION ALL\n" .
        "    SELECT\n" .
        "        me.emt_id user_id,\n" .
        "        'EMT' role\n" .
        "    FROM urgence\n" .
        "        JOIN mission_emts me\n" .
        "        ON me.mission_id = urgence.id\n" .
        "    WHERE is_deleted = 0\n" .
        "    AND dep_to_site BETWEEN ? AND ?) q\n" .
        "LEFT JOIN users u\n" .
        "    ON u.id = q.user_id\n" .
        "GROUP BY u.name,\n" .
        "        q.role\n" .
        "ORDER BY u.name, q.role\n";

        $data = \DB::select($query , [$this->transform($startDate) . " 00:00:00",
            $this->transform($endDate) . " 23:59:59",
            $this->transform($startDate) . " 00:00:00",
            $this->transform($endDate) . " 23:59:59",
            $this->transform($startDate) . " 00:00:00",
            $this->transform($endDate) . " 23:59:59"]);

        Excel::create("Role Report", function ($excel) use ($data) {
            $excel
                ->setTitle("Role Report")
                ->setCreator("IT")
                ->setCompany("LRC 502")
                ->setDescription("!!");

            $excel->sheet("Role Report", function ($sheet) use ($data) {
                $sheet->row(1, ["EMT", "Role", "Count"]);

                $sheet->row(1, function ($row) {
                    // call cell manipulation methods
                    $row->setBackground("#939FA3");
                    $row->setAlignment("center");
                    $row->setVAlignment("center");
                    $row->setFontSize(16);
                    $row->setFontWeight("bold");
                });

                $sheet->freezeFirstRow();
                $sheet->cells("A2:C" . (count($data) + 1), function (
                    $cells
                ) {
                    $cells->setAlignment("left");
                    $cells->setVAlignment("top");
                    $cells->setFontSize(14);
                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:C" . (count($data) + 1), "thick");

                $pos = 2;
                foreach ($data as $row) {
                    $sheet->row($pos, [ $row->name, $row->role, $row->count]);
                    $pos += 1;
                }
            });
        })->download("xls");
    }

    public function createMissionTypeReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        if (true) {
            $types = \App\Http\ORM\Urgence::select(
                "type as T",
                \DB::raw("count(1) as total")
            )
                ->whereYear("dep_to_site", "=", $year)
                ->whereMonth("dep_to_site", "=", $month)
                ->where("is_deleted", 0)
                ->groupBy("type")
                ->get();

            Excel::create(
                "Mission_Type_Report_" . $month . "_" . $year,
                function ($excel) use ($types) {
                    $excel
                        ->setTitle("Mission Type report for 502")
                        ->setCreator("IT")
                        ->setCompany("LRC 502")
                        ->setDescription("!!");

                    $excel->sheet("Mission Type", function ($sheet) use (
                        $types
                    ) {
                        $sheet->row(1, ["Mission Type", "Count"]);

                        $sheet->row(1, function ($row) {
                            // call cell manipulation methods
                            $row->setBackground("#939FA3");
                            $row->setAlignment("center");
                            $row->setVAlignment("center");
                            $row->setFontSize(16);
                            $row->setFontWeight("bold");
                        });

                        $sheet->freezeFirstRow();
                        $sheet->cells("A2:B" . (count($types) + 1), function (
                            $cells
                        ) {
                            $cells->setAlignment("left");
                            $cells->setVAlignment("top");
                            $cells->setFontSize(14);
                        });
                        $sheet->setAutoSize(true);
                        $sheet->setBorder(
                            "A1:B" . (count($types) + 1),
                            "thick"
                        );

                        $pos = 2;
                        foreach ($types as $u) {
                            $sheet->row($pos, [
                                \App\Http\ORM\mission_type::where(
                                    "id",
                                    $u->T
                                )->first()->name,
                                $u->total,
                            ]);
                            $pos += 1;
                        }
                    });
                }
            )->download("xls");
        } else {
            return;
        }
    }

    public function createSupportReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $year = Input::get("year");
        $month = Input::get("month");

        if ($month == "" || !is_numeric($month) || $month < 0 || $month > 12) {
            return "Month Error. Please Contact IT";
        }

        if ($year == "" || !is_numeric($year)) {
            return "Year Error. Please Contact IT";
        }

        if (true) {
            $data = Urgence::whereMonth("dep_to_site", $month)
                ->whereYear("dep_to_site", $year)
                ->where("is_deleted", 0)
                ->get();

            //Not annuler
            $urg = [];
            $ann = [];

            foreach ($data as $urgence) {
                //We want all missions where we left from Location outise 502
                try {
                    if (
                        strpos($urgence->from, "1") === 0 ||
                        strpos($urgence->from, "2") === 0
                    ) {
                        //From Location, get Location
                        $f = explode(".", $urgence->from)[1];
                        $from = Village::where("id", $f)->first();
                        if ($from != null && $from->station != 502) {
                            $urgence->type = "Urgence";
                            $urgence->from = $from->village;
                            $urgence->station = $from->station;
                            $urg[] = $urgence;
                        }
                    } elseif (strpos($urgence->from, "0") === 0) {
                        //From Hospital
                        $f = explode(".", $urgence->from)[1];
                        $from = Hospital::where("id", $f)->first();
                        if ($from != null && $from->station != 502) {
                            if (strpos($urgence->to, "0") === 0) {
                                $urgence->type = "Urgence";
                            } else {
                                $urgence->type = "Transport";
                            }
                            $urgence->from =
                                $from->name . " ( " . $from->code . " ) ";
                            $urgence->station = $from->station;
                            $urg[] = $urgence;
                        }
                    } elseif ($urgence->from === "3.502") {
                        //From Station
                        $t = explode(".", $urgence->to);
                        if ($t[0] == 1 || $t[0] == 2) {
                            $to = Village::where("id", $t[1])->first();
                            if ($to != null && $to->station != 502) {
                                $urgence->type = "Urgence";
                                $urgence->from = "C 502";
                                $urgence->station = $to->station;
                                $ann[] = $urgence;
                            }
                        } elseif ($t[0] == 0) {
                            $to = Hospital::where("id", $t[1])->first();
                            if ($to != null && $to->station != 502) {
                                $urgence->type = "Transport";
                                $urgence->from = "C 502";
                                $urgence->station = $to->station;
                                $ann[] = $urgence;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error($e);
                    return "An Error has Occured, please contact IT";
                }
            }

            Excel::create("Support_" . $month . "_" . $year, function (
                $excel
            ) use ($urg, $ann) {
                $excel
                    ->setTitle("Support")
                    ->setCreator("IT")
                    ->setCompany("LRC 502")
                    ->setDescription("!!");

                $excel->sheet("Missions", function ($sheet) use ($urg) {
                    $sheet->row(1, [
                        "Date",
                        "Name",
                        "Case",
                        "Location",
                        "Hospital",
                        "Type of mission",
                        "Mission #",
                        "Dispatcher",
                        "Station",
                        "Comment",
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
                    $sheet->cells("A2:J" . (count($urg) + 1), function (
                        $cells
                    ) {
                        $cells->setAlignment("left");
                        $cells->setVAlignment("top");
                        $cells->setFontSize(14);
                    });
                    $sheet->setAutoSize(true);
                    $sheet->setBorder("A1:J" . (count($urg) + 1), "thick");

                    $pos = 2;
                    foreach ($urg as $u) {
                        $urge = $this->setTo($u);
                        $sheet->row($pos, [
                            explode(" ", $urge->dep_to_site)[0], //'Date',
                            str_replace(" %%% ", " ", $urge->patient_name), //'Name',
                            $urge->patient_case, //'Case',
                            $urge->from, //'Location',
                            $urge->to, //'Hospital',
                            $urge->type, //'Type of mission',
                            $pos - 1, //'Mission #',
                            "Gamma", //'Dispatcher',
                            $urge->station, //'Station',
                            $urge->comment, //'Comment'
                        ]);
                        $pos += 1;
                    }
                });

                $excel->sheet("Annuler", function ($sheet) use ($ann) {
                    $sheet->row(1, [
                        "Date",
                        "Name",
                        "Case",
                        "Location",
                        "Hospital",
                        "Type of mission",
                        "Mission #",
                        "Dispatcher",
                        "Station",
                        "Comment",
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
                    $sheet->cells("A2:J" . (count($ann) + 1), function (
                        $cells
                    ) {
                        $cells->setAlignment("left");
                        $cells->setVAlignment("top");
                        $cells->setFontSize(14);
                    });
                    $sheet->setAutoSize(true);
                    $sheet->setBorder("A1:J" . (count($ann) + 1), "thick");

                    $pos = 2;
                    foreach ($ann as $u) {
                        $urge = $this->setTo($u);
                        $sheet->row($pos, [
                            explode(" ", $urge->dep_to_site)[0], //'Date',
                            str_replace(" %%% ", " ", $urge->patient_name), //'Name',
                            $urge->patient_case, //'Case',
                            $urge->from, //'Location',
                            $urge->to, //'Hospital',
                            $urge->type, //'Type of mission',
                            $pos - 1, //'Mission #',
                            "Gamma", //'Dispatcher',
                            $urge->station, //'Station',
                            $urge->comment, //'Comment'
                        ]);
                        $pos += 1;
                    }
                });
            })->download("xls");
        } else {
            return;
        }
    }

    public function createFirstAidReport()
    {
        if (!Auth::check()) {
            return "Unauthorized access. Please Login";
        }

        $access = Auth::user()->memberOf("Statistic");

        if (!$access) {
            return "Unauthorized access";
        }

        $startDate = Input::get("StartDate");
        $endDate = Input::get("EndDate");

        if ($this->verfiyDate($startDate) && $this->verfiyDate($endDate)) {
            $headers = [
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Content-type" => "text/csv",
                "Content-Disposition" =>
                    "attachment; filename=First Aid Report" .
                    $this->transform($startDate) .
                    "---" .
                    $this->transform($endDate) .
                    ".csv",
                "Expires" => "0",
                "Pragma" => "public",
            ];

            $data = Urgence::select(
                "dep_to_site",
                "patient_name",
                "patient_sex",
                "patient_age",
                "patient_nationality",
                "patient_case as chief_complaint",
                "comment"
            )
                ->whereBetween("dep_to_site", [
                    $this->transform($startDate) . " 00:00:00",
                    $this->transform($endDate) . " 23:59:59",
                ])
                ->where("type", 13)
                ->where("is_deleted", 0)
                ->orderBy("dep_to_site")
                ->get();
            $result = [];
            $header = [
                "Notes ملاحظات",
                "Description وصف الحالة",
                "Nationality الجنسية",
                "Age العمر",
                "Gender الجنس",
                "Name الاسم",
                "Time التوقيت",
                "Date التاريخ ",
            ];
            $res[] = $header;
            foreach ($data as $urg) {
                $row = [];
                $row[] = $urg->comment;
                $row[] = $urg->chief_complaint;
                $row[] = Nationality::where("id", $urg->patient_nationality)
                    ->pluck("name")
                    ->first();
                $row[] = intval(date("Y")) - intval($urg->patient_age);
                $row[] =
                    $urg->patient_sex == "Male" ? "Male/ذكر" : "Female/أنثى";
                $row[] = str_replace("%%%", "", $urg->patient_name);
                $row[] =
                    explode(":", explode(" ", $urg->dep_to_site)[1])[0] .
                    ":" .
                    explode(":", explode(" ", $urg->dep_to_site)[1])[1];
                $row[] = explode(" ", $urg->dep_to_site)[0];
                $res[] = $row;
            }

            # add headers for each column in the CSV download
            //array_unshift($list, array_keys($list[0]));

            $callback = function () use ($res) {
                $FH = fopen("php://output", "w");
                fprintf($FH, chr(0xef) . chr(0xbb) . chr(0xbf));
                foreach ($res as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };

            return Response::stream($callback, 200, $headers);
        } else {
            return;
        }
    }

    public function index()
    {
        date_default_timezone_set("Asia/Beirut");

        $date = Request::get("Date");
        $report = [];

        if ($date == "") {
            $date = Date("Y-m-d");
            //$date = DateTime::createFromFormat('Y-m-d',$date)->modify('-1 day')->format('Y-m-d');
        }

        $date = DateTime::createFromFormat("Y-m-d", $date);

        if (!is_object($date)) {
            $date = Date("Y-m-d");
            $date = DateTime::createFromFormat("Y-m-d", $date)
                ->modify("-1 day")
                ->format("Y-m-d");
        }

        $emergencies = Urgence::select(
            "dep_to_site",
            "ret_to_centre",
            "ambulance_id",
            "id",
            "parent_id",
            "patient_name",
            "patient_age",
            "patient_case",
            "type",
            "material_used",
            "from",
            "from_other",
            "to",
            "to_other",
            "o2",
            "other_emts",
            "comment",
            "checked"
        )
            ->where("dep_to_site", ">=", $date->format("Y-m-d"))
            ->where(
                "dep_to_site",
                "<=",
                $date->modify("+1 day")->format("Y-m-d")
            )
            ->where("is_deleted", 0)
            ->orderBy("dep_to_site")
            // ->orderBy('created_at')
            ->get()
            ->groupBy("parent_id");

        $date = $date->modify("-1 day");

        $types = \DB::select(
            "SELECT mission_type.name,mission_type.id as id,count( distinct(urgence.parent_id) ) as count
                            FROM urgence
                            LEFT JOIN mission_type
                            ON urgence.type = mission_type.id
                            WHERE urgence.dep_to_site >= '" .
                $date->format("Y-m-d") .
                "'
                            AND urgence.dep_to_site <= '" .
                $date->modify("+1 day")->format("Y-m-d") .
                "' 
			    AND urgence.is_deleted = 0 
                            GROUP BY urgence.type
                            ORDER BY mission_type.id ASC"
        );
        $casualties = false;
        $date = $date->modify("-1 day");
        foreach ($types as $type) {
            if ($type->id == 1) {
                //3adad isabet bl hades

                $tt = [];
                $tt["name"] = $type->name;
                $tt["count"] = $type->count;
                $tt["casualties"] = \DB::select(
                    "Select count(*) as count from urgence 
                                            where urgence.call_time >= '" .
                        $date->format("Y-m-d") .
                        "'
                                            AND urgence.call_time <= '" .
                        $date->modify("+1 day")->format("Y-m-d") .
                        "' and urgence.to like '0.%' and urgence.type = 1"
                )[0]->count;
                $date = $date->modify("-1 day");
                $report[] = $tt;
                $casualties = true;
            } else {
                $tt = [];
                $tt["name"] = $type->name;
                $tt["count"] = $type->count;
                $report[] = $tt;
            }
        }
        if (count($report) == 0) {
            $tt = [];
            $tt["name"] = "- -";
            $tt["count"] = "- -";
            $report[] = $tt;
        }

        $tableData = [];
        $count = 1;
        $comme = "";
        foreach ($emergencies as $key) {
            $count2 = 1;

            foreach ($key as $k) {
                $record = [];
                $record[] = $k->id;
                $record[] = $k->checked;
                if (count($key) > 1) {
                    $record[] = $count . "." . $count2;
                    $count2 += 1;
                } else {
                    $record[] = $count;
                }

                if ($k->comment != "") {
                    $comme .=
                        "في المهمة رقم" .
                        " " .
                        $record[2] .
                        " - " .
                        $k->comment .
                        "\n";
                }

                $record[] = DateTime::createFromFormat(
                    "Y-m-d H:i:s",
                    $k->dep_to_site
                )->format("H:i");
                $record[] = DateTime::createFromFormat(
                    "Y-m-d H:i:s",
                    $k->ret_to_centre
                )->format("H:i");

                $amb = ambulance::select("car_number")
                    ->where("id", $k->ambulance_id)
                    ->first();
                $record[] = $amb == null ? "-" : $amb->car_number;

                //dRIVER m leader emt
                $names_ar = [];

                $driver = $k->driver()->first();
                if (!is_null($driver)) {
                    $names_ar[] = EMT::select("name", "nickname")
                        ->where("id", $driver->driver_id)
                        ->first();
                }

                $leader = $k->leader()->first();

                if (!is_null($leader)) {
                    $names_ar[] = EMT::select("name", "nickname")
                        ->where("id", $leader->mission_leader_id)
                        ->first();
                }

                $emts = $k->emts()->get();
                if (!is_null($emts) && count($emts) > 0) {
                    foreach ($emts as $emt) {
                        error_log($emt->id);
                        $names_ar[] = EMT::select("name", "nickname")
                            ->where("id", $emt->emt_id)
                            ->first();
                    }
                }

                if (!is_null($k->other_emts) && $k->other_emts != "") {
                    $o = new \stdClass();
                    $o->nickname = $k->other_emts;
                    $names_ar[] = $o;
                }

                $names = "";
                foreach ($names_ar as $name) {
                    if ($names != "") {
                        $names .= " & ";
                    }
                    if ($name->nickname == "") {
                        $names .= $name->name;
                    } else {
                        $names .= $name->nickname;
                    }
                }

                $record[] = $names;
                $record[] =
                    $k->patient_name == ""
                        ? "- -"
                        : str_replace("%%% ", "", $k->patient_name);
                $record[] =
                    $k->patient_age == ""
                        ? "- -"
                        : date("Y") -
                            $k->patient_age .
                            " (" .
                            $k->patient_age .
                            ")";
                $record[] = $k->patient_case == "" ? "- -" : $k->patient_case;
                $record[] = mission_type::where("id", $k->type)->first()->name;
                //$record[] = $k->material()->pluck('id');
                //from
                $from = $k->from;
                if ($from == "4") {
                    $record[] = $k->from_other;
                } else {
                    //hospital
                    $tt = explode(".", $from);
                    if ($tt[0] == 0) {
                        $h = Hospital::where("id", $tt[1])->first();
                        $record[] = $h->name . " (" . $h->code . ")";
                    } elseif ($tt[0] == 1) {
                        $h = Village::where("id", $tt[1])->first();
                        $record[] =
                            "Δ " . $h->village . " (" . $h->station . ")";
                    } elseif ($tt[0] == 2) {
                        $h = Village::where("id", $tt[1])->first();
                        $record[] =
                            "عريض " . $h->village . " (" . $h->station . ")";
                    } elseif ($tt[0] == 3) {
                        $c = station::where("id", $tt[1])->first();
                        $record[] = "C" . $c->id . " (" . $c->name . ")";
                    }
                }

                //to
                $to = $k->to;
                if ($to == "4") {
                    $record[] = $k->to_other;
                } else {
                    //hospital
                    $tt = explode(".", $to);
                    if ($tt[0] == 0) {
                        $h = Hospital::where("id", $tt[1])->first();
                        $record[] = $h->name . " (" . $h->code . ")";
                    } elseif ($tt[0] == 1) {
                        $h = Village::where("id", $tt[1])->first();
                        $record[] =
                            "Δ " . $h->village . " (" . $h->station . ")";
                    } elseif ($tt[0] == 2) {
                        $h = Village::where("id", $tt[1])->first();
                        $record[] =
                            "عريض " . $h->village . " (" . $h->station . ")";
                    } elseif ($tt[0] == 3) {
                        $c = station::where("id", $tt[1])->first();
                        $record[] = "C" . $c->id . " (" . $c->name . ")";
                    }
                }

                $record[] = $k->o2;

                $surveys = \DB::table("survey")
                    ->where("survey.mission_id", $k->id)
                    ->join("users", "users.id", "=", "survey.user_id")
                    ->select("survey.is_filled", "users.name")
                    ->get();

				$record[] = $surveys;

                $tableData[] = $record;
            }
            $count += 1;
        }
        $count = count($tableData);
        $date = $date->format("Y-m-d");
        $comm = comment::where("date", $date)->first();

        $from = "";
        $to = "";

        if (!is_null($comm)) {
            $from =
                is_null($comm->from) || $comm->from == ""
                    ? ""
                    : EMT::select("name", "nickname")
                        ->where("id", $comm->from)
                        ->first();
            if (!is_null($from) && $from != "") {
                if ($from->nickname == "") {
                    $from = $from->name;
                } else {
                    $from = $from->name . " (" . $from->nickname . ")";
                }
            }
            $to = is_null($comm->to) ? "" : $comm->to;
        }
        //$comm = is_null($comm) ? "" : $comm->content;

        $emts = EMT::select("name", "nickname", "id")
            ->where("is_deleted", 0)
            ->orderBy("name", "asc")
            ->get();

        foreach ($emts as $emt) {
            if ($emt->nickname == "") {
                $emt->nickname = $emt->name;
            } else {
                $emt->nickname = $emt->name . " (" . $emt->nickname . ")";
            }
        }

        //  $report = str_replace(array("\n","\r"),"9999",$report);
        $comm = str_replace(["\n", "\r"], "9999", $comme);

        return view(
            "report",
            compact(
                "tableData",
                "count",
                "comm",
                "date",
                "emts",
                "from",
                "to",
                "report",
                "casualties"
            )
        );
    }
}
