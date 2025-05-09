<?php
namespace App\Services;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleMeetService
{
    public function createMeeting($startTime, $endTime)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/service-account.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->useApplicationDefaultCredentials();

        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'موعد جديد',
            'start' => [
                'dateTime' => Carbon::parse($startTime)->toRfc3339String(),
                'timeZone' => 'Africa/Cairo',
            ],
            'end' => [
                'dateTime' => Carbon::parse($endTime)->toRfc3339String(),
                'timeZone' => 'Africa/Cairo',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ]);

        $calendarId = '';
        $createdEvent = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        return $createdEvent->getHangoutLink();
    }
}
