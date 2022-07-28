<?php


use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DB;

class CityController extends PageController
{
    private static $allowed_actions = [
        'byCountryCode',
        'suggest',
    ];

    public function index(HTTPRequest $request)
    {
        // OLD CODE 

        // $datas = $request->requestVars();

        // $where = "";
        // // FILTER BY Name 
        // if (isset($datas['Name'])) {
        //     $filterName = strtoupper($datas['Name']);
        //     $where = "WHERE Name LIKE '%{$filterName}%'";
        // }

        // // FILTER BY CountryCode 
        // if (isset($datas['CountryCode'])) {
        //     $filterCountryCode = strtoupper($datas['CountryCode']);
        //     if ($where == "") {
        //         $where = "WHERE CountryCode LIKE '%{$filterCountryCode}%'";
        //     } else {
        //         $where = $where . " AND CountryCode LIKE '%{$filterCountryCode}%'";
        //     }
        // }

        // // FILTER BY District 
        // if (isset($datas['District'])) {
        //     $filterDistrict = strtoupper($datas['District']);
        //     if ($where == "") {
        //         $where = "WHERE District LIKE '%{$filterDistrict}%'";
        //     } else {
        //         $where = $where . " AND District LIKE '%{$filterDistrict}%'";
        //     }
        // }

        // // FILTER BY POPULATION 
        // if (isset($datas['minPopulation']) && isset($datas['maxPopulation'])) {
        //     // Validate Range Population
        //     $minPopulation = str_replace('.', '', $datas['minPopulation']);
        //     $maxPopulation = str_replace('.', '', $datas['maxPopulation']);

        //     if ($minPopulation >= $maxPopulation) {
        //         $response = [
        //             "status" => [
        //                 "code" => 422,
        //                 "description" => "Unprocessable Entity",
        //                 "message" => [
        //                     'Masukkan range populasi yang valid'
        //                 ]
        //             ]
        //         ];
        //         $this->response->addHeader('Content-Type', 'application/json');
        //         return json_encode($response);
        //         die;
        //     } elseif ($maxPopulation > $minPopulation) {
        //         if ($where == "") {
        //             $where = " WHERE Population BETWEEN $minPopulation AND $maxPopulation";
        //         } else {
        //             $where = $where . " AND Population BETWEEN $minPopulation AND $maxPopulation";
        //         }
        //     }
        // } elseif (isset($datas['minPopulation']) || isset($datas['maxPopulation'])) {
        //     if (isset($datas['minPopulation'])) {
        //         $minPopulation = str_replace('.', '', $datas['minPopulation']);

        //         if ($where == "") {
        //             $where = "WHERE Population >= " . $minPopulation;
        //         } else {
        //             $where = $where . " AND Population >= " . $minPopulation;
        //         }
        //     }
        //     if (isset($datas['maxPopulation'])) {
        //         $maxPopulation = str_replace('.', '', $datas['maxPopulation']);

        //         if ($where == "") {
        //             $where = "WHERE Population <= " . $maxPopulation;
        //         } else {
        //             $where = $where . " AND Population <= " . $maxPopulation;
        //         }
        //     }
        // }

        // // SORTING 
        // $sortBy = "ID";
        // $order = "ASC";

        // if (isset($datas['sortBy'])) {
        //     $sortBy = $datas['sortBy'];
        // }
        // if (isset($datas['order'])) {
        //     $order = $datas['order'];
        // }

        // $sql = "SELECT City.*
        // FROM City
        // $where
        // ORDER BY $sortBy $order";

        // $cityData = DB::query($sql);

        // if ($cityData->numRecords() > 0) {
        //     $temp_results = [];
        //     foreach ($cityData as $city) {
        //         $data = array();
        //         $data['ID'] = $city['ID'];
        //         $data['Name'] = $city['Name'];
        //         $data['CountryCode'] = $city['CountryCode'];
        //         $data['District'] = $city['District'];
        //         $data['Population'] = $city['Population'];

        //         $temp_results[] = $data;
        //     }

        //     $response = [
        //         "status" => [
        //             "code" => 200,
        //             "description" => "OK",
        //             "message" => [
        //                 'Hasil pencarian data.'
        //             ]
        //         ],
        //         "data" => $temp_results
        //     ];
        // } else {
        //     $response = [
        //         "status" => [
        //             "code" => 404,
        //             "description" => "Not Found",
        //             "message" => [
        //                 'Data tidak ditemukan.'
        //             ]
        //         ]
        //     ];
        // }

        // $this->response->addHeader('Content-Type', 'application/json');
        // return json_encode($response);

        // END OLD CODE 

        $sortBy = "ID";
        $order = "ASC";
        $where = "";
        $keyword = "";

        $datas = $request->requestVars();
        if (isset($datas['search'])) {
            $key = $datas['search'];
            $keyword = Convert::raw2sql($key);
            $keyword = strtoupper($keyword);
            $keyword = str_replace('%', '', $keyword);

            $where = " WHERE Name LIKE '%" . $keyword . "%'
            OR CountryCode LIKE '%" . $keyword . "%'
            OR District LIKE '%" . $keyword . "%'
            OR Population  LIKE '%" . $keyword . "%'";
        }

        if (isset($datas['sortBy'])) {
            $sortBy = $datas['sortBy'];
        }
        if (isset($datas['order'])) {
            $order = $datas['order'];
        }

        $sql = "SELECT City.*
        FROM City
        $where
        ORDER BY $sortBy $order";

        $dataCity = DB::query($sql);

        if ($dataCity->numRecords() > 0) {
            $temp_results = [];
            foreach ($dataCity as $city) {
                $data = array();
                $data['ID'] = $city['ID'];
                $data['Name'] = $city['Name'];
                $data['CountryCode'] = $city['CountryCode'];
                $data['District'] = $city['District'];
                $data['Population'] = $city['Population'];
                $temp_results[] = $data;
            }

            if (trim($keyword) == null) {
                $message = 'List Data City';
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

    public function byCountryCode(HTTPRequest $request)
    {
        // OLD CODE 

        // $datas = $request->requestVars();
        // $CountryCode = $request->params()["ID"];

        // $andWhere = "";
        // // FILTER BY Name 
        // if (isset($datas['Name'])) {
        //     $filterName = strtoupper($datas['Name']);
        //     $andWhere = $andWhere . " AND Name LIKE '%{$filterName}%'";
        // }

        // // FILTER BY District 
        // if (isset($datas['District'])) {
        //     $filterDistrict = strtoupper($datas['District']);
        //     $andWhere = $andWhere . " AND District LIKE '%{$filterDistrict}%'";
        // }

        // // FILTER BY POPULATION 
        // if (isset($datas['minPopulation']) && isset($datas['maxPopulation'])) {
        //     // Validate Range Population
        //     $minPopulation = str_replace('.', '', $datas['minPopulation']);
        //     $maxPopulation = str_replace('.', '', $datas['maxPopulation']);

        //     if ($minPopulation >= $maxPopulation) {
        //         $response = [
        //             "status" => [
        //                 "code" => 422,
        //                 "description" => "Unprocessable Entity",
        //                 "message" => [
        //                     'Masukkan range populasi yang valid'
        //                 ]
        //             ]
        //         ];
        //         $this->response->addHeader('Content-Type', 'application/json');
        //         return json_encode($response);
        //         die;
        //     } elseif ($maxPopulation > $minPopulation) {
        //         $andWhere = $andWhere . " AND Population BETWEEN $minPopulation AND $maxPopulation";
        //     }
        // } elseif (isset($datas['minPopulation']) || isset($datas['maxPopulation'])) {
        //     if (isset($datas['minPopulation'])) {
        //         $minPopulation = str_replace('.', '', $datas['minPopulation']);
        //         $andWhere = $andWhere . " AND Population >= " . $minPopulation;
        //     }
        //     if (isset($datas['maxPopulation'])) {
        //         $maxPopulation = str_replace('.', '', $datas['maxPopulation']);
        //         $andWhere = $andWhere . " AND Population <= " . $maxPopulation;
        //     }
        // }

        // // SORTING 
        // $sortBy = "ID";
        // $order = "ASC";

        // if (isset($datas['sortBy'])) {
        //     $sortBy = $datas['sortBy'];
        // }
        // if (isset($datas['order'])) {
        //     $order = $datas['order'];
        // }

        // $sql = "SELECT City.*
        // FROM City
        // WHERE CountryCode = '" . $CountryCode . "' $andWhere
        // ORDER BY $sortBy $order";

        // $cityData = DB::query($sql);

        // if ($cityData->numRecords() > 0) {
        //     $temp_results = [];
        //     foreach ($cityData as $city) {
        //         $data = array();
        //         $data['ID'] = $city['ID'];
        //         $data['Name'] = $city['Name'];
        //         $data['District'] = $city['District'];
        //         $data['Population'] = $city['Population'];

        //         $temp_results[] = $data;
        //     }

        //     $response = [
        //         "status" => [
        //             "code" => 200,
        //             "description" => "OK",
        //             "message" => [
        //                 'Data City By CountryCode ' . $CountryCode
        //             ]
        //         ],
        //         "data" => $temp_results
        //     ];
        // } else {
        //     $response = [
        //         "status" => [
        //             "code" => 404,
        //             "description" => "Not Found",
        //             "message" => [
        //                 'Data tidak ditemukan.'
        //             ]
        //         ]
        //     ];
        // }

        // $this->response->addHeader('Content-Type', 'application/json');
        // return json_encode($response);

        // END OLD CODE 

        $datas = $request->requestVars();
        $CountryCode = $request->params()["ID"];

        $sortBy = "ID";
        $order = "ASC";
        $andWhere = "";
        $keyword = "";

        $datas = $request->requestVars();
        if (isset($datas['search'])) {
            $key = $datas['search'];
            $keyword = Convert::raw2sql($key);
            $keyword = strtoupper($keyword);
            $keyword = str_replace('%', '', $keyword);

            $andWhere = " AND (Name LIKE '%" . $keyword . "%'
            OR District LIKE '%" . $keyword . "%'
            OR Population  LIKE '%" . $keyword . "%')";
        }

        if (isset($datas['sortBy'])) {
            $sortBy = $datas['sortBy'];
        }
        if (isset($datas['order'])) {
            $order = $datas['order'];
        }

        $sql = "SELECT City.*
        FROM City
        WHERE CountryCode = '" . $CountryCode . "' $andWhere
        ORDER BY $sortBy $order";

        $dataCity = DB::query($sql);

        if ($dataCity->numRecords() > 0) {
            $temp_results = [];
            foreach ($dataCity as $city) {
                $data = array();
                $data['ID'] = $city['ID'];
                $data['Name'] = $city['Name'];
                $data['CountryCode'] = $city['CountryCode'];
                $data['District'] = $city['District'];
                $data['Population'] = $city['Population'];
                $temp_results[] = $data;
            }

            if (trim($keyword) == null) {
                $message = 'List Data City By CountryCode ' . $CountryCode;
            } else {
                $message = 'Hasil pencarian data City By CountryCode ' . $CountryCode . ' dengan keyword ' . $key;
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
                        'Data City By CountryCode ' . $CountryCode . ' dengan keyword ' . $key . ' tidak ditemukan.'
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
            (SELECT Name AS suggest
            FROM City 
            WHERE Name LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT CountryCode AS suggest
            FROM City 
            WHERE CountryCode LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT District AS suggest
            FROM City 
            WHERE District LIKE '%" . strtoupper($query) . "%'
            UNION ALL
            SELECT Population AS suggest
            FROM City 
            WHERE Population LIKE '%" . strtoupper($query) . "%')
            tablenya GROUP BY suggest";

            $dataCity = DB::query($sql);

            if ($dataCity->numRecords() > 0) {
                $suggest_results = [];
                foreach ($dataCity as $city) {
                    array_push($suggest_results, $city['suggest']);
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
