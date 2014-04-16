C2DM Component for Yii Framework 1
========

**NOTE**: C2DM Has been deprecated.

This is a very lightweight and very simple extension that allows you to easily access Android's Cloud to Device Messaging API through Yii.

##Requirements

* Yii 1.1 or above
* cURL

##Usage

Some configuration is needed in the component section:

~~~
<?php
//...
'c2dm' => array(
        'class' => 'application.extensions.c2dm.C2DM',
        'username' => 'YOUR_C2DM_USERNAME',
        'password' => 'YOUR_C2DM_PASSWORD',
        'applicationIdentifier' => 'com.c2dm.app', // Whatever your app identifier is
),
~~~

Then you can simply push to the device:

~~~
Yii::app()->c2dm->push('PUSH_REGISTRATION_ID', array('msg' => 'Message to Send'));
~~~

##Spec
~~~
array push( mixed $registrationIDs , array $message [, bool $delay_while_idle [, string $collapse_key ] ] )
~~~
The parameters are:

1. $registrationIDs; a string containing a single registration ID or an array of IDs.  If this is an array, the message will be sent to each ID.

2. $message; an array of key value pairs to be sent through the Android C2DM API.  Please ensure that this is less than 256 bytes.

3. $delay_while_idle; If set as true, this will wait until the device wakes up to send the push notification.  (Good for unimportant, passive notifications).

3. $collapse_key [optional]; "An arbitrary string that is used to collapse a group of like messages when the device is offline, so that only the last message gets sent to the client. This is intended to avoid sending too many messages to the phone when it comes back online. Note that since there is no guarantee of the order in which messages get sent, the "last" message may not actually be the last message sent by the application server."  If one is not provided, it will be generated based on the MD5 Hash of the $message array.

- push returns an array of arrays.  Each array contains:
 * [0] string, The ID the message was pushed to.
 * [1] bool, Whether or not there was an error.
 * [2] string, The response from the C2DM Service.
