<?php
$apiKey = 'wFM3KxSh5wecnDUKGSYs85CDxbQwzIU4N5TfM';
$sheet1Id = 'Pm5whjjPCXJXxCQc46WXmMMpx54R39WGQxF9Xxw1';
$sheet2Id = 'qR5C32vwxqQJHGjFgMvPq6JJ7Gvwfwm9h7h5Ff51';
$sheet3Id = 'JHWpPvj5p8JvwfC8qGvxHR5XWCFJcpVprmJXpxQ1';

$url_sheet1 = "https://api.smartsheet.com/2.0/sheets/$sheet1Id";
$url_sheet2 = "https://api.smartsheet.com/2.0/sheets/$sheet2Id";
$url_sheet3 = "https://api.smartsheet.com/2.0/sheets/$sheet3Id";

$emailvalue = isset($_GET['email']) ? $_GET['email'] : '';

function fetchSheetData($url, $apiKey) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);
    if ($httpCode !== 200) {
        throw new Exception("Error: HTTP Code $httpCode - $response");
    }
    return json_decode($response, true);
}

try {
    $data_sheet1 = fetchSheetData($url_sheet1, $apiKey);
    $data_sheet2 = fetchSheetData($url_sheet2, $apiKey);
    $data_sheet3 = fetchSheetData($url_sheet3, $apiKey);

    // Debugging: Output the fetched data for inspection
    // Uncomment these lines if needed for debugging
    // echo '<pre>'; print_r($data_sheet1); echo '</pre>';
    // echo '<pre>'; print_r($data_sheet2); echo '</pre>';
    // echo '<pre>'; print_r($data_sheet3); echo '</pre>';

    $colEmail = 4;
    $colWorkshopGroup = 22;
    $colWorkshopName = 23;

    $workshopSessions_sheet1 = [];
    foreach ($data_sheet1['rows'] as $row) {
        $rowData = $row['cells'];
        $email = isset($rowData[$colEmail - 1]['value']) ? htmlspecialchars($rowData[$colEmail - 1]['value']) : '';
        $workshopGroup = isset($rowData[$colWorkshopGroup - 1]['value']) ? htmlspecialchars($rowData[$colWorkshopGroup - 1]['value']) : '';
        $workshopName = isset($rowData[$colWorkshopName - 1]['value']) ? htmlspecialchars($rowData[$colWorkshopName - 1]['value']) : '';

        if ($email === $emailvalue) {
            $workshopKey = $workshopGroup . '|' . $workshopName;
            if (!isset($workshopSessions_sheet1[$workshopKey])) {
                $workshopSessions_sheet1[$workshopKey] = [
                    'workshopGroup' => $workshopGroup,
                    'workshopName' => $workshopName,
                    'sessionCount_sheet1' => 0,
                ];
            }
            $workshopSessions_sheet1[$workshopKey]['sessionCount_sheet1']++;
        }
    }

    $colWorkshopGroup_sheet2 = 2;
    $colWorkshopName_sheet2 = 1;

    $sessionCounts_sheet2 = [];
    foreach ($data_sheet2['rows'] as $row) {
        $rowData_sheet2 = $row['cells'];
        $workshopGroup_sheet2 = isset($rowData_sheet2[$colWorkshopGroup_sheet2 - 1]['value']) ? htmlspecialchars($rowData_sheet2[$colWorkshopGroup_sheet2 - 1]['value']) : '';
        $workshopName_sheet2 = isset($rowData_sheet2[$colWorkshopName_sheet2 - 1]['value']) ? htmlspecialchars($rowData_sheet2[$colWorkshopName_sheet2 - 1]['value']) : '';
        $workshopKey_sheet2 = $workshopGroup_sheet2 . '|' . $workshopName_sheet2;

        if (!isset($sessionCounts_sheet2[$workshopKey_sheet2])) {
            $sessionCounts_sheet2[$workshopKey_sheet2] = 0;
        }
        $sessionCounts_sheet2[$workshopKey_sheet2]++;
    }

    $colWorkshopName_sheet3 = 1;
    $competencyColumns = [2, 3, 4, 5, 6];

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Data</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .custom-dropdown-menu {
            display: none;
            background-color: #fff;
            border: 1px solid #ddd;
            position: absolute;
            z-index: 1000;
        }
        .custom-dropdown-menu div {
            padding: 10px;
            cursor: pointer;
        }
        .custom-dropdown-menu div:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="btn-group">
        <button class="btn btn-secondary btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="workshopGroup">Please Select Workshop Group</button>
        <div class="dropdown-menu custom-dropdown-menu workshop-group-dropdown">
            <div data-value="Show All">Show All</div>
            <div data-value="Career & Self-Development">Career & Self-Development</div>
            <div data-value="Effectiveness & Well-Being">Effectiveness & Well-Being</div>
            <div data-value="Leadership & Management">Leadership & Management</div>
            <div data-value="Oral & Written Communication">Oral & Written Communication</div>
            <div data-value="Teamwork & Collaboration">Teamwork & Collaboration</div>
        </div>
    </div>

    <div class="enroll_projection">
    <table id="dtBasicExample2" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="width:25%;">Workshop Name</th>
                <th class="career">Career & Self-Development</th>
                <th class="effectiveness">Effectiveness & Well-Being</th>
                <th class="leadership">Leadership & Management</th>
                <th class="communication">Oral & Written Communication</th>
                <th class="teamwork">Teamwork & Collaboration</th>
                <th style="width:10%;">Status</th>
            </tr>
        </thead>
        <tbody class="acad_plan">';

    foreach ($data_sheet3['rows'] as $row) {
        $rowData_sheet3 = $row['cells'];
        $workshopName_sheet3 = isset($rowData_sheet3[$colWorkshopName_sheet3 - 1]['value']) ? htmlspecialchars($rowData_sheet3[$colWorkshopName_sheet3 - 1]['value']) : '';

        $status = 'Pending';
        $workshopKey = '';
        foreach ($workshopSessions_sheet1 as $key => $data) {
            if ($data['workshopName'] === $workshopName_sheet3) {
                $workshopKey = $key;
                break;
            }
        }

        if ($workshopKey && isset($sessionCounts_sheet2[$workshopKey])) {
            $sessionCount_sheet1 = $workshopSessions_sheet1[$workshopKey]['sessionCount_sheet1'];
            $sessionCount_sheet2 = $sessionCounts_sheet2[$workshopKey];
            $status = ($sessionCount_sheet1 == $sessionCount_sheet2) ? 'Completed' : 'Pending';
        }

        echo '<tr class="workshop-row">';
        echo '<td>' . $workshopName_sheet3 . '</td>';

        foreach ($competencyColumns as $colIndex) {
            $competency = isset($rowData_sheet3[$colIndex - 1]['value']) ? htmlspecialchars($rowData_sheet3[$colIndex - 1]['value']) : '';
            $checked = $competency ? 'checked' : '';
            $checkbox = $competency ? '<input type="checkbox" class="active-checkbox" onclick="return false" onkeydown="return false" ' . $checked . '>' : '';
            $class = '';
            switch ($colIndex) {
                case 2: $class = 'career'; break;
                case 3: $class = 'effectiveness'; break;
                case 4: $class = 'leadership'; break;
                case 5: $class = 'communication'; break;
                case 6: $class = 'teamwork'; break;
            }
            echo '<td class="' . $class . '">' . $checkbox . '</td>';
        }

        echo '<td>' . $status . '</td>';
        echo '</tr>';
    }

    echo '</tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#dtBasicExample2").DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "pagingType": "simple",
            "lengthChange": false
        });

        $(".custom-dropdown-menu div").click(function() {
            var selectedValue = $(this).data("value");
            $("#workshopGroup").text($(this).text());

            var normalizedSelectedValue = selectedValue.toLowerCase().replace(/ & /g, '').replace(/ /g, '');

            $(".workshop-row").each(function() {
                var showRow = false;

                if (selectedValue === "Show All") {
                    showRow = true;
                } else {
                    $(this).find("td").each(function() {
                        var cellClass = $(this).attr("class");
                        var normalizedCellClass = cellClass ? cellClass.toLowerCase().replace(/ & /g, '').replace(/ /g, '') : '';
                        
                        if (normalizedCellClass === normalizedSelectedValue) {
                            if ($(this).find("input:checked").length > 0) {
                                showRow = true;
                            }
                        }
                    });
                }

                if (showRow) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $(document).click(function(event) {
            if (!$(event.target).closest(".btn-group").length) {
                $(".custom-dropdown-menu").hide();
            }
        });

        $("#workshopGroup").click(function() {
            $(".custom-dropdown-menu").toggle();
        });
    });
    </script>
</body>
</html>';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>