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
        $pages = 1;
        $limit = 10;

        $datas = $request->requestVars();

        // Validate Pagination 
        if (isset($datas['pages'])) {
            $pages = $datas['pages'];
        }
        if (isset($datas['limit'])) {
            $limit = $datas['limit'];
        }
        $offset = ($pages - 1) * $limit;

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
        FROM Country";

        $dataCountry = DB::query($sql);
        $total_records = $dataCountry->numRecords();

        $searchOrder = "$where ORDER BY $sortBy $order";
        $dataCountry = DB::query($sql . $searchOrder);
        $count_results = $dataCountry->numRecords();

        $dataCountry = DB::query($sql . $searchOrder . " LIMIT $limit OFFSET $offset");

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
                "data" => [
                    "pages" => $pages,
                    "limit" => $limit,
                    "total_records" => $total_records,
                    "count_results" => $count_results,
                    "results" => $temp_results
                ]
            ];
        } else {
            $response = [
                "status" => [
                    "code" => 404,
                    "description" => "Not Found",
                    "message" => [
                        'Data dengan keyword ' . $key . ' tidak ditemukan.'
                    ]
                ]
            ];
        }

        $this->response->addHeader('Content-Type', 'application/json');
        return json_encode($response);
    }

    public function suggest(HTTPRequest $request)
    {
        $datas = $request->requestVars();
        if (isset($datas['keyword']) && str_replace('%', '', trim($datas['keyword'])) != null) {
            $query = $datas['keyword'];
            $query = Convert::raw2sql($query);
            $sql = "SELECT suggest FROM 
            (SELECT Code AS suggest
            FROM Country 
            WHERE Code LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Name AS suggest
            FROM Country 
            WHERE Name LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Continent AS suggest
            FROM Country 
            WHERE Continent LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Region AS suggest
            FROM Country 
            WHERE Region LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT SurfaceArea AS suggest
            FROM Country 
            WHERE SurfaceArea LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT IndepYear AS suggest
            FROM Country 
            WHERE IndepYear LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Population AS suggest
            FROM Country 
            WHERE Population LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT LifeExpectancy AS suggest
            FROM Country 
            WHERE LifeExpectancy LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT GNP AS suggest
            FROM Country 
            WHERE GNP LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT GNPOld AS suggest
            FROM Country 
            WHERE GNPOld LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT LocalName AS suggest
            FROM Country 
            WHERE LocalName LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT GovernmentForm AS suggest
            FROM Country 
            WHERE GovernmentForm LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT HeadOfState AS suggest
            FROM Country 
            WHERE HeadOfState LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Capital AS suggest
            FROM Country 
            WHERE Capital LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Code2 AS suggest
            FROM Country 
            WHERE Code2 LIKE '%" . strtoupper($query) . "%'
            )
            tablenya GROUP BY suggest";

            $dataCountry = DB::query($sql);
            if ($dataCountry->numRecords() > 0) {
                $suggest_results = [];
                foreach ($dataCountry as $country) {
                    array_push($suggest_results, $country['suggest']);
                }

                $response = [
                    "status" => [
                        "code" => 200,
                        "description" => "OK",
                        "message" => [
                            'Suggestion untuk ' . $query
                        ]
                    ],
                    "data" => $suggest_results
                ];
            } else {
                $response = [
                    "status" => [
                        "code" => 404,
                        "description" => "Not Found",
                        "message" => [
                            'Suggestion tidak ditemukan'
                        ]
                    ]
                ];
            }
        } else {
            $response = [
                "status" => [
                    "code" => 422,
                    "description" => "Unprocessable Entity",
                    "message" => [
                        'Masukkan keyword yang valid'
                    ]
                ]
            ];
        }

        $this->response->addHeader('Content-Type', 'application/json');
        return json_encode($response);
    }
}
