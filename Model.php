<?php

class Model
{
    private $appId;
    private $appKey;
    private $api;

    public function __construct($appId, $appKey, $api)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->api = $api;
    }

    private function HTTP_GET($URL, $HTTPHEADER)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $HTTPHEADER);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function HTTP_POST($URL, $HTTPHEADER, $POSTFIELDS)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $HTTPHEADER);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function HTTP_PUT($URL, $HTTPHEADER, $POSTFIELDS)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $HTTPHEADER);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function GET($URL)
    {
        $HTTPHEADER = array("X-LC-Id: {$this->appId}", "X-LC-Key: {$this->appKey}");
        $URL = $this->api . $URL;
        return $this->HTTP_GET($URL, $HTTPHEADER);
    }

    private function POST($URL, $POSTFIELDS)
    {
        $HTTPHEADER = array("X-LC-Id: {$this->appId}", "X-LC-Key: {$this->appKey}", 'Content-Type: application/json');
        $URL = $this->api . $URL;
        return $this->HTTP_POST($URL, $HTTPHEADER, $POSTFIELDS);
    }

    private function PUT($URL, $POSTFIELDS)
    {
        $HTTPHEADER = array("X-LC-Id: {$this->appId}", "X-LC-Key: {$this->appKey}", 'Content-Type: application/json');
        $URL = $this->api . $URL;
        return $this->HTTP_PUT($URL, $HTTPHEADER, $POSTFIELDS);
    }

    private function iferr($var)
    {
        if (empty($var)) {
            http_response_code(404);
            echo "404 Error (Not Found or Server Error. You Can Try Again)";
            exit;
        }
        return $var;
    }

    public function archive($market, $date)
    {
        $where = urlencode(json_encode(array('market' => $market, 'date' => $date)));
        $URL = 'classes/Archive?where=' . $where . '&include=image';
        return $this->iferr(json_decode($this->GET($URL), true)['results'])[0];
    }

    public function archives($date)
    {
        $where = urlencode(json_encode(array('date' => $date)));
        $URL = 'classes/Archive?where=' . $where . '&order=-market&include=image';
        $archives = $this->iferr(json_decode($this->GET($URL), true)['results']);
        $list = [];
        foreach ($archives as $n => $archive) {
            $list[$archive['image']['objectId']][] = $archive;
        }
        return $list;
    }

    public function image($imageName)
    {
        $where = urlencode(json_encode([
            "image" => [
                '$inQuery' => [
                    "className" => 'Image',
                    "where" => [
                        "name" => $imageName
                    ]
                ]
            ]
        ]));
        $URL = 'classes/Archive?where=' . $where . '&order=-date,-market&include=image';
        return $this->iferr(json_decode($this->GET($URL), true)['results']);
    }

    public function images($page, $eachPage)
    {
        $skip = $page - 1;
        $skip = $eachPage * $skip;
        $URL = 'classes/Image?order=-createdAt&skip=' . $skip . '&limit=' . $eachPage;
        return $this->iferr(json_decode($this->GET($URL), true)['results']);
    }
}
