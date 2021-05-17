<?php

namespace App\Services;

use Google\Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class GoogleDriveService
{
    private Client $client;
    private Google_Service_Drive $service;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('Google Drive API PHP Quickstart');
        $client->setScopes(Google_Service_Drive::DRIVE);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new \Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        $this->client = $client;
        $this->service = new Google_Service_Drive($this->client);
    }

    private function createBackupDirectory(): Google_Service_Drive_DriveFile
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setName('database_backups');
        $file->setKind('drive.file');
        $file->setMimeType('application/vnd.google-apps.folder');

        $params = [
            'uploadType' => 'multipart',
        ];

        return $this->service->files->create($file, $params);
    }

    private function getBackupDirectory(): Google_Service_Drive_DriveFile
    {
        $optParams = array(
            'pageSize' => 50,
            'fields' => 'nextPageToken, files(id, name)',
            'q' => 'mimeType="application/vnd.google-apps.folder"',
        );
        $results = $this->service->files->listFiles($optParams);
        
        if (count($results->getFiles()) == 0) {
            $directory = $this->createBackupDirectory();
        } else {
            $directory = array_filter($results->getFiles(), function (Google_Service_Drive_DriveFile $file) {
                return $file->getName() == 'database_backups';
            })[0];

            if (!$directory) {
                $directory = $this->createBackupDirectory();
            }
        }
        return $directory;        
    }

    public function uploadFile(string $fileName, string $fileContent)
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($fileName);
        $file->setKind('drive.file');
        $file->setParents([$this->getBackupDirectory()['id']]);

        $params = [
            'data' => $fileContent,
            'uploadType' => 'resumable'
        ];

        return $this->service->files->create($file, $params);
    }
}
