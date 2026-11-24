<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\User;
use App\Http\ORM\Group;
use App\Http\ORM\GroupMember;

class PrivateFunct
{
	public static function SIDtoString($ADsid)
	{
		$sid = "S-";
		//$ADguid = $info[0]['objectguid'][0];
		$sidinhex = str_split(bin2hex($ADsid), 2);
		// Byte 0 = Revision Level
		$sid = $sid . hexdec($sidinhex[0]) . "-";
		// Byte 1-7 = 48 Bit Authority
		$sid = $sid . hexdec($sidinhex[6] . $sidinhex[5] . $sidinhex[4] . $sidinhex[3] . $sidinhex[2] . $sidinhex[1]);
		// Byte 8 count of sub authorities - Get number of sub-authorities
		$subauths = hexdec($sidinhex[7]);
		//Loop through Sub Authorities
		for ($i = 0; $i < $subauths; $i++) {
			$start = 8 + (4 * $i);
			// X amount of 32Bit (4 Byte) Sub Authorities
			$sid = $sid . "-" . hexdec($sidinhex[$start + 3] . $sidinhex[$start + 2] . $sidinhex[$start + 1] . $sidinhex[$start]);
		}
		return $sid;
	}

	public static function authenticate($username, $password)
	{

		$success = false;

		// An empty password makes many LDAP servers accept the bind as an
		// "unauthenticated bind", which would log anyone in as any user.
		if (!is_string($username) || trim($username) === '' || !is_string($password) || $password === '') {
			return 0;
		}

		// Escape the username so it cannot alter the search filter
		$username = ldap_escape(trim($username), '', LDAP_ESCAPE_FILTER);

		try {

			$ldap_con = ldap_connect(env('LDAP_CONTROLLERS'));
			ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

			$ldap_dn = env('LDAP_ADMIN_USERNAME', '');
			$ldap_password = env('LDAP_ADMIN_PASSWORD', '');
			$ldap_active_dn = env('LDAP_ACTIVE_DN', '');

			if (ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {

				$arr = array('dn');
				$result = ldap_search($ldap_con, $ldap_active_dn, "(sAMAccountName=$username)", $arr);
				$entries = ldap_get_entries($ldap_con, $result);

				if ($entries['count'] > 0) {
					if (ldap_bind($ldap_con, $entries[0]['dn'], $password)) {
						$success = true;
					}
				}
			}
			ldap_close($ldap_con);

			if ($success)
				return 1;
		} catch (\ErrorException $e) {
			Log::warning('Wrong Credentials');
		} catch (\Exception $e) {
			Log::warning($e);
		}
		return 0;
	}

	private static function store($user, $deleted)
	{

		$entries = array();
		$i = 0;
		$entries[$i] = $user;

		$ldap_unique_field = env('LDAP_UNIQUE_FIELD', '');

		//If objects id not set, exit
		if (!isset($entries[$i][$ldap_unique_field][0])) {
			Log::info($ldap_unique_field . " not found");
			return;
		}

		if ($ldap_unique_field == "objectsid") {
			$id = self::SIDtoString($entries[$i][$ldap_unique_field][0]);
		} else {
			$id = $entries[$i][$ldap_unique_field][0];
		}

		$user = User::updateOrCreate(['objectsid' => $id]);

		//Name
		if (isset($entries[$i]["displayname"][0])) {

			$user->name = $entries[$i]["displayname"][0];
			//array_push($data, "name", $entries[$i]["displayname"][0]);
		}

		//Nickname
		if (isset($entries[$i]["samaccountname"][0])) {

			$user->nickname = $entries[$i]["samaccountname"][0];
			//array_push($data, "nickname", $entries[$i]["samaccountname"][0]);
		}

		//Email
		if (isset($entries[$i]["mail"][0])) {

			$user->email = $entries[$i]["mail"][0];
			//array_push($data, "email", $entries[$i]["mail"][0]);
		}

		//Mobile
		if (isset($entries[$i]["telephonenumber"][0])) {

			$user->mobile = $entries[$i]["telephonenumber"][0];
			//array_push($data, "mobile", $entries[$i]["mobile"][0]);
		}

		//Is Driver
		if (isset($entries[$i]["memberof"]) && array_search(env("LDAP_DRIVERS", ''), $entries[$i]["memberof"]) !== false) {

			$user->driver = true;
		} else {
			$user->driver = false;
		}

		//Active
		$user->is_deleted = $deleted;
		$user->save();
		$user_groups = GroupMember::where('user_id', $user->id)->get();
		foreach ($user_groups as $user_group) {

			$user_group->delete();
		}
		/*if( isset($entries[$i]["memberof"]) && $entries[$i]["samaccountname"][0] == "Tayy502" ) {
Log::warning($entries[$i]["memberof"]);
}*/
		$groups = Group::get();
		foreach ($groups as $group) {

			if (isset($entries[$i]["memberof"]) && array_search($group->address, $entries[$i]["memberof"]) !== false) {
				$group_member = new GroupMember;
				$group_member->user_id = $user->id;
				$group_member->group_id = $group->id;
				$group_member->save();
			}
		}
	}


	public static function sync_users()
	{

		$ldap_dn = env('LDAP_ADMIN_USERNAME', '');
		$ldap_password = env('LDAP_ADMIN_PASSWORD', '');
		$ldap_con = ldap_connect(env('LDAP_CONTROLLERS', ''), 389);
		$ldap_active_dn = env('LDAP_ACTIVE_DN', '');
		$ldap_inactive_dn = env('LDAP_INACTIVE_DN', '');
		$ldap_unique_field = env('LDAP_UNIQUE_FIELD', '');

		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

		$attr = array(
			$ldap_unique_field,
			"displayname",
			"samaccountname",
			"mail",
			"telephonenumber",
			"memberof"
		);

		try {

			if (ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {
				$filter = "cn=*";
				$result = ldap_search($ldap_con, $ldap_active_dn, $filter, $attr) or exit("Unable to search");
				$entries = ldap_get_entries($ldap_con, $result);
				for ($i = 0; $i < $entries["count"]; $i++) {
					self::store($entries[$i], 0);
				}
				$result = ldap_search($ldap_con, $ldap_inactive_dn, $filter, $attr) or exit("Unable to search");
				$entries = ldap_get_entries($ldap_con, $result);
				for ($i = 0; $i < $entries["count"]; $i++) {
					self::store($entries[$i], 1);
				}
			} else {
				return "Invalid user/pass or other errors!";
			}
		} catch (\Exception $e) {
			Log::error($e);
		}

		return "Success";
	}
}
