<?php
/* Copyright (C) 2026 Pierre Ardoin <developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Complete substitution array with shipping contacts data.
 *
 * @param array     $substitutionarray Substitution array
 * @param Translate $outputlangs       Output language
 * @param mixed     $object            Object used to find linked contacts
 * @param array     $parameters        Hook parameters
 * @return void
 */
function deliveryaddress_completesubstitutionarray(&$substitutionarray, $outputlangs, $object = null, $parameters = null)
{
	global $db;

	deliveryaddress_init_shipping_substitution_array($substitutionarray);

	if (!is_object($object) || !method_exists($object, 'liste_contact')) {
		return;
	}

	dol_include_once('/contact/class/contact.class.php');
	dol_include_once('/user/class/user.class.php');

	$externalContactItem = deliveryaddress_get_first_shipping_contact($object, 'external');
	if (!empty($externalContactItem['id'])) {
		$contact = new Contact($db);
		if ($contact->fetch($externalContactItem['id']) > 0) {
			$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLNAME__'] = dolGetFirstLastname($contact->firstname, $contact->lastname);
			$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLADDRESS__'] = deliveryaddress_format_address($contact, $outputlangs);
			$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_PHONE__'] = deliveryaddress_join_phones($contact->phone_pro, $contact->phone_mobile);
			$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_EMAIL__'] = empty($contact->email) ? '' : $contact->email;
		}
	}

	$internalContactItem = deliveryaddress_get_first_shipping_contact($object, 'internal');
	if (!empty($internalContactItem['id'])) {
		$user = new User($db);
		if ($user->fetch($internalContactItem['id']) > 0) {
			$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLNAME__'] = dolGetFirstLastname($user->firstname, $user->lastname);
			$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLADDRESS__'] = deliveryaddress_format_address($user, $outputlangs);
			$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_PHONE__'] = deliveryaddress_join_phones($user->office_phone, $user->user_mobile);
			$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_EMAIL__'] = empty($user->email) ? '' : $user->email;
		}
	}
}

/**
 * Initialize all delivery address shipping substitutions with empty strings.
 *
 * @param array $substitutionarray Substitution array
 * @return void
 */
function deliveryaddress_init_shipping_substitution_array(&$substitutionarray)
{
	$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLNAME__'] = '';
	$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLADDRESS__'] = '';
	$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_PHONE__'] = '';
	$substitutionarray['__DELIVERYADDRESS_EXTERNAL_SHIPPING_EMAIL__'] = '';
	$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLNAME__'] = '';
	$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLADDRESS__'] = '';
	$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_PHONE__'] = '';
	$substitutionarray['__DELIVERYADDRESS_INTERNAL_SHIPPING_EMAIL__'] = '';
}

/**
 * Return first linked contact with SHIPPING code for a source.
 *
 * @param CommonObject $object Object with contacts
 * @param string       $source Contact source: external or internal
 * @return array
 */
function deliveryaddress_get_first_shipping_contact($object, $source)
{
	$contacts = $object->liste_contact(-1, $source);
	if (!is_array($contacts)) {
		return array();
	}

	foreach ($contacts as $contact) {
		if (!empty($contact['code']) && $contact['code'] === 'SHIPPING') {
			return $contact;
		}
	}

	return array();
}

/**
 * Return a formatted postal address without contact or company name.
 *
 * @param mixed     $object      Contact or user object
 * @param Translate $outputlangs Output language
 * @return string
 */
function deliveryaddress_format_address($object, $outputlangs)
{
	if (!function_exists('dol_format_address')) {
		require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
	}

	return dol_format_address($object, 1, "\n", $outputlangs);
}

/**
 * Join office/pro phone and mobile phone.
 *
 * @param string $phone  Main phone
 * @param string $mobile Mobile phone
 * @return string
 */
function deliveryaddress_join_phones($phone, $mobile)
{
	$phones = array();

	if (!empty($phone)) {
		$phones[] = $phone;
	}
	if (!empty($mobile)) {
		$phones[] = $mobile;
	}

	return implode(' / ', $phones);
}
