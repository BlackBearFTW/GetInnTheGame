<?php


if (isset($_POST['iUserID']) && is_numeric($_POST['iUserID'])) {


    if ($stmt = mysqli_prepare($link, "SELECT points FROM users users.user_id = ?")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "i", $iUserID);

        /* execute query */
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $iPointCount = $row['points'];
        }

        /* close statement */
        mysqli_stmt_close($stmt);
    }


    if ($stmt = mysqli_prepare($link, "SELECT badge_id, img_path FROM badge INNER JOIN challenge_status AS ch_stats ON ch_stats.badge_id = badge.badge_id WHERE ch_stats.user_id = ?")) {

        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "i", $iUserID);

        /* execute query */
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {

            echo '<img src="/images/', $row['img_path'], '" alt="">';
        }

        /* close statement */
        mysqli_stmt_close($stmt);
    }
}
