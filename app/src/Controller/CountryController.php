<?php


use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DB;

class CountryController extends PageController
{
    private static $allowed_actions = [
        'suggest'
    ];

    public function index(HTTPRequest $request)
    {
        $sortBy = "Code";
        $order = "ASC";
        $where = "";
        $keyword = "";

        $datas = $request->requestVars();
        if (isset($datas['search'])) {
            $key = $datas['search'];
            $keyword = Convert::raw2sql($key);
            $keyword = strtoupper($keyword);
            $keyword = str_replace('%', '', $keyword);

            $where = " WHERE Code LIKE '%" . $keyword . "%'
            OR Name LIKE '%" . $keyword . "%'
            OR Continent LIKE '%" . $keyword . "%'
            OR Region LIKE '%" . $keyword . "%'
            OR SurfaceArea LIKE '%" . $keyword . "%'
            OR IndepYear LIKE '%" . $keyword . "%'
            OR Population LIKE '%" . $keyword . "%'
            OR LifeExpectancy LIKE '%" . $keyword . "%'
            OR GNP LIKE '%" . $keyword . "%'
            OR GNPOld LIKE '%" . $keyword . "%'
            OR LocalName LIKE '%" . $keyword . "%'
            OR GovernmentForm LIKE '%" . $keyword . "%'
            OR HeadOfState LIKE '%" . $keyword . "%'
            OR Capital LIKE '%" . $keyword . "%'
            OR Code2 LIKE '%" . $keyword . "%'";
        }

        if (isset($datas['sortBy'])) {
            $sortBy = $datas['sortBy'];
        }
        if (isset($datas['order'])) {
            $order = $datas['order'];
        }

        $sql = "SELECT Country.*
        FROM Country
        $where
        ORDER BY $sortBy $order";
        $dataCountry = DB::query($sql);

        if ($dataCountry->numRecords() > 0) {
            $temp_results = [];
            foreach ($dataCountry as $country) {
                $data = array();
                $data['Code'] = $country['Code'];
                $data['Name'] = $country['Name'];
                $data['Continent'] = $country['Continent'];
                $data['Region'] = $country['Region'];
                $data['SurfaceArea'] = $country['SurfaceArea'];
                $data['IndepYear'] = $country['IndepYear'];
                $data['Population'] = $country['Population'];
                $data['LifeExpectancy'] = $country['LifeExpectancy'];
                $data['GNP'] = $country['GNP'];
                $data['GNPOld'] = $country['GNPOld'];
                $data['LocalName'] = $country['LocalName'];
                $data['GovernmentForm'] = $country['GovernmentForm'];
                $data['HeadOfState'] = $country['HeadOfState'];
                $data['Capital'] = $country['Capital'];
                $data['Code2'] = $country['Code2'];

                $temp_results[] = $data;
            }

            if (trim($keyword) == null) {
                $message = 'List Data Country';
            } else {
                $message = 'Hasil pencarian data dengan keyword ' . $key;
            }

            $response = [
                "status" => [
                    "code" => 200,
                    "description" => "OK",
                    "message" => [
                        $message
                    ]
                ],
                "data" => $temp_results
            ];
        } else {
            $response = [
                "status" => [
                    "code" => 404,
                    "description" => "Not Found",
                    "message" => [
                        'Data dengan keyword ' . $keyword . ' tidak ditemukan.'
                    ]
                ]
            ];
        }

        $this->response->addHeader('Content-Type', 'application/json');
        return json_encode($response);
    }

    public function suggest(HTTPRequest $request)
    {
        // 
    }
}
