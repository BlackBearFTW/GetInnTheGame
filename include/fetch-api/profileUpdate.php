<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/include/dbConfig.php";


if (isset($_POST['iUserID']) && is_numeric($_POST['iUserID'])) {

    $iUserID = (int) $_POST['iUserID'];


    if ($stmt = mysqli_prepare($link, "SELECT points FROM users WHERE users.user_id = ?")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "i", $iUserID);

        /* execute query */
        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result($stmt, $iPointCount);

        mysqli_stmt_fetch($stmt);

        /* close statement */
        mysqli_stmt_close($stmt);
    }


    if ($stmt = mysqli_prepare($link, "SELECT img_path FROM badge WHERE badge_id IN (SELECT badge_id FROM challenge_status WHERE user_id = ? AND status = 'COMPLETED')")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "i", $iUserID);

        /* execute query */
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $row['completed'] = true;
            $aBadgeData[] = $row;
        }
    }

    if ($stmt = mysqli_prepare($link, "SELECT img_path FROM badge WHERE badge_id NOT IN (SELECT badge_id FROM challenge_status WHERE user_id = ? AND status != 'COMPLETED')")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "i", $iUserID);

        /* execute query */
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $row['completed'] = false;
            $aBadgeDataUncompleted[] = $row;
        }

        $aBadgeDataFinal = array_merge($aBadgeData, $aBadgeDataUncompleted);

        /* close statement */
        mysqli_stmt_close($stmt);
    }

    echo json_encode([
        "pointCount" => $iPointCount,
        "badges" => $aBadgeDataFinal
    ]);

    // var_dump($aBadgeDataFinal);
}
