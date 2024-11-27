<?php
class EtherpadLiteClient {

    const API_VERSION             = "1.3.0";

    const CODE_OK                 = 0;
    const CODE_INVALID_PARAMETERS = 1;
    const CODE_INTERNAL_ERROR     = 2;
    const CODE_INVALID_FUNCTION   = 3;
    const CODE_INVALID_API_KEY    = 4;

    protected $apiKey = "";
    protected $baseUrl = "http://localhost:9001/api";
  
    public function __construct($apiKey, $baseUrl = null) {
        if (strlen($apiKey) < 1) {
          throw new InvalidArgumentException("[{$apiKey}] is not a valid API key");
        }
        $this->apiKey  = $apiKey;
        if (isset($baseUrl)) {
            $this->baseUrl = $baseUrl;
        }
        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("[{$this->baseUrl}] is not a valid URL");
        }
    }

    protected function get($function, array $arguments = []) {
        return $this->call($function, $arguments, 'GET');
    }

    protected function post($function, array $arguments = []) {
        return $this->call($function, $arguments, 'POST');
    }

    protected function convertBools($candidate) {
        if (is_bool($candidate)) {
          return $candidate? "true" : "false";
        }
        return $candidate;
      }

    protected function call($function, array $arguments = [], $method = 'GET') {
        $arguments['apikey'] = $this->apiKey;
        $arguments = array_map(array($this, 'convertBools'), $arguments);
        $url = $this->baseUrl."/".self::API_VERSION."/".$function;

        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_TIMEOUT, 20);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($arguments));
        $result = curl_exec($c);
        //file_put_contents('logs.txt', $result.PHP_EOL , FILE_APPEND | LOCK_EX);
        $result = json_decode($result);
        //file_put_contents('logs.txt', $result.PHP_EOL , FILE_APPEND | LOCK_EX);
        curl_close($c);
        
        if(!$result) {
            throw new UnexpectedValueException("Empty or No Response from the server");
        }
        
        // $result = json_decode($result);
        if ($result === null) {
            throw new UnexpectedValueException("JSON response could not be decoded");
        }

        return $this->handleResult($result);
    }

    protected function handleResult($result) {
        if (!isset($result->code)) {
            throw new RuntimeException("API response has no code");
        }
        if (!isset($result->message)) {
            throw new RuntimeException("API response has no message");
        }
        if (!isset($result->data)) {
            $result->data = null;
        }

        switch ($result->code) {
            case self::CODE_OK:
                return $result->data;
            case self::CODE_INVALID_PARAMETERS:
            case self::CODE_INVALID_API_KEY:
                throw new InvalidArgumentException($result->message);
            case self::CODE_INTERNAL_ERROR:
                throw new RuntimeException($result->message);
            case self::CODE_INVALID_FUNCTION:
                throw new BadFunctionCallException($result->message);
            default:
                throw new RuntimeException("An unexpected error occurred whilst handling the response");
        }
    }

    // createGroup
    public function createGroup() {
        $params = [];

        return $this->post("createGroup", $params);
    }

    // createGroupIfNotExistsFor
    public function createGroupIfNotExistsFor($groupMapper) {
        $params = [];

        $params['groupMapper'] = $groupMapper;

        return $this->post("createGroupIfNotExistsFor", $params);
    }

    // deleteGroup
    public function deleteGroup($groupID) {
        $params = [];

        $params['groupID'] = $groupID;

        return $this->post("deleteGroup", $params);
    }

    // listPads
    public function listPads($groupID) {
        $params = [];

        $params['groupID'] = $groupID;

        return $this->get("listPads", $params);
    }

    // listAllPads
    public function listAllPads() {
        $params = [];

        return $this->get("listAllPads", $params);
    }

    // createDiffHTML
    public function createDiffHTML($padID, $startRev, $endRev) {
        $params = [];

        $params['padID'] = $padID;
        $params['startRev'] = $startRev;
        $params['endRev'] = $endRev;

        return $this->post("createDiffHTML", $params);
    }

    // createPad
    public function createPad($padID, $text = null) {
        $params = [];

        $params['padID'] = $padID;
        if (isset($text)) {
            $params['text'] = $text;
        }

        return $this->post("createPad", $params);
    }

    // createGroupPad
    public function createGroupPad($groupID, $padName, $text = null) {
        $params = [];

        $params['groupID'] = $groupID;
        $params['padName'] = $padName;
        if (isset($text)) {
            $params['text'] = $text;
        }

        return $this->post("createGroupPad", $params);
    }

    // createAuthor
    public function createAuthor($name = null) {
        $params = [];

        if (isset($name)) {
            $params['name'] = $name;
        }

        return $this->post("createAuthor", $params);
    }

    // createAuthorIfNotExistsFor
    public function createAuthorIfNotExistsFor($authorMapper, $name = null) {
        $params = [];

        $params['authorMapper'] = $authorMapper;
        if (isset($name)) {
            $params['name'] = $name;
        }

        return $this->post("createAuthorIfNotExistsFor", $params);
    }

    // listPadsOfAuthor
    public function listPadsOfAuthor($authorID) {
        $params = [];

        $params['authorID'] = $authorID;

        return $this->get("listPadsOfAuthor", $params);
    }

    // createSession
    public function createSession($groupID, $authorID, $validUntil) {
        $params = [];
    
        $params['groupID'] = $groupID;
        $params['authorID'] = $authorID;
        $params['validUntil'] = $validUntil;
    
        return $this->post("createSession", $params);
    }

    // deleteSession
    public function deleteSession($sessionID) {
        $params = [];

        $params['sessionID'] = $sessionID;

        return $this->post("deleteSession", $params);
    }

    // getSessionInfo
    public function getSessionInfo($sessionID) {
        $params = [];

        $params['sessionID'] = $sessionID;

        return $this->get("getSessionInfo", $params);
    }

    // listSessionsOfGroup
    public function listSessionsOfGroup($groupID) {
        $params = [];

        $params['groupID'] = $groupID;

        return $this->get("listSessionsOfGroup", $params);
    }

    // listSessionsOfAuthor
    public function listSessionsOfAuthor($authorID) {
        $params = [];

        $params['authorID'] = $authorID;

        return $this->get("listSessionsOfAuthor", $params);
    }

    // getText
    public function getText($padID, $rev = null) {
        $params = [];

        $params['padID'] = $padID;
        if (isset($rev)) {
            $params['rev'] = $rev;
        }

        return $this->get("getText", $params);
    }

    // setText
    public function setText($padID, $text) {
        $params = [];

        $params['padID'] = $padID;
        $params['text'] = $text;

        return $this->post("setText", $params);
    }

    // getHTML
    public function getHTML($padID, $rev = null) {
        $params = [];

        $params['padID'] = $padID;
        if (isset($rev)) {
            $params['rev'] = $rev;
        }

        return $this->get("getHTML", $params);
    }

    // setHTML
    public function setHTML($padID, $html) {
        $params = [];

        $params['padID'] = $padID;
        $params['html'] = $html;

        return $this->post("setHTML", $params);
    }

    // getAttributePool
    public function getAttributePool($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getAttributePool", $params);
    }

    // getRevisionsCount
    public function getRevisionsCount($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getRevisionsCount", $params);
    }

    // getSavedRevisionsCount
    public function getSavedRevisionsCount($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getSavedRevisionsCount", $params);
    }

    // listSavedRevisions
    public function listSavedRevisions($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("listSavedRevisions", $params);
    }

    // saveRevision
    public function saveRevision($padID, $rev) {
        $params = [];

        $params['padID'] = $padID;
        $params['rev'] = $rev;

        return $this->post("saveRevision", $params);
    }

    // getRevisionChangeset
    public function getRevisionChangeset($padID, $rev = null) {
        $params = [];

        $params['padID'] = $padID;
        if (isset($rev)) {
        $params['rev'] = $rev;
        }

        return $this->get("getRevisionChangeset", $params);
    }

    // getLastEdited
    public function getLastEdited($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getLastEdited", $params);
    }

    // deletePad
    public function deletePad($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->post("deletePad", $params);
    }

    // copyPad
    public function copyPad($sourceID, $destinationID, $force = null) {
        $params = [];

        $params['sourceID'] = $sourceID;
        $params['destinationID'] = $destinationID;
        if (isset($force)) {
            $params['force'] = $force;
        }

        return $this->post("copyPad", $params);
    }

    // movePad
    public function movePad($sourceID, $destinationID, $force = null) {
        $params = [];

        $params['sourceID'] = $sourceID;
        $params['destinationID'] = $destinationID;
        if (isset($force)) {
            $params['force'] = $force;
        }

        return $this->post("movePad", $params);
    }

    // getReadOnlyID
    public function getReadOnlyID($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getReadOnlyID", $params);
    }

    // getPadID
    public function getPadID($roID) {
        $params = [];

        $params['roID'] = $roID;

        return $this->get("getPadID", $params);
    }

    // setPublicStatus
    public function setPublicStatus($padID, $publicStatus) {
        $params = [];

        $params['padID'] = $padID;
        $params['publicStatus'] = $publicStatus;

        return $this->post("setPublicStatus", $params);
    }

    // getPublicStatus
    public function getPublicStatus($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getPublicStatus", $params);
    }

    // setPassword
    public function setPassword($padID, $password) {
        $params = [];

        $params['padID'] = $padID;
        $params['password'] = $password;

        return $this->post("setPassword", $params);
    }

    // isPasswordProtected
    public function isPasswordProtected($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("isPasswordProtected", $params);
    }

    // listAuthorsOfPad
    public function listAuthorsOfPad($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("listAuthorsOfPad", $params);
    }

    // padUsersCount
    public function padUsersCount($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("padUsersCount", $params);
    }

    // getAuthorName
    public function getAuthorName($authorID) {
        $params = [];

        $params['authorID'] = $authorID;

        return $this->get("getAuthorName", $params);
    }

    // padUsers
    public function padUsers($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("padUsers", $params);
    }

    // sendClientsMessage
    public function sendClientsMessage($padID, $msg) {
        $params = [];

        $params['padID'] = $padID;
        $params['msg'] = $msg;

        return $this->post("sendClientsMessage", $params);
    }

    // listAllGroups
    public function listAllGroups() {
        $params = [];


        return $this->get("listAllGroups", $params);
    }

    // checkToken
    public function checkToken() {
        $params = [];


        return $this->post("checkToken", $params);
    }

    // getChatHistory
    public function getChatHistory($padID, $start = null, $end = null) {
        $params = [];

        $params['padID'] = $padID;
        if (isset($start)) {
            $params['start'] = $start;
        }
        if (isset($end)) {
            $params['end'] = $end;
        }

        return $this->get("getChatHistory", $params);
    }

    // getChatHead
    public function getChatHead($padID) {
        $params = [];

        $params['padID'] = $padID;

        return $this->get("getChatHead", $params);
    }

    // restoreRevision
    public function restoreRevision($padID, $rev) {
        $params = [];

        $params['padID'] = $padID;
        $params['rev'] = $rev;

        return $this->post("restoreRevision", $params);
    }
}
