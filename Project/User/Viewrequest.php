<?php
include("../Assets/Connection/connection.php");
session_start();
ob_start();
include("Head.php");

$curDate = Date('Y-m-d');
$viewrequest = "SELECT * FROM tbl_request r 
                INNER JOIN tbl_bloodgroup b ON r.bloodgroup_id = b.blood_id 
                INNER JOIN tbl_type t ON t.type_id = r.type_id 
                INNER JOIN tbl_place p ON p.place_id = r.place_id 
                INNER JOIN tbl_district d ON d.district_id = p.district_id 
                INNER JOIN tbl_user u ON u.user_id = r.user_id 
                WHERE r.user_id !='" . $_SESSION['uid'] . "' 
                AND request_status = 0 
                AND request_requireddate >= '$curDate' 
                ORDER BY r.request_id DESC";
$resprofile = $Conn->query($viewrequest);

// $datauser = $resprofile->fetch_assoc();
?>

<?php
if (isset($_GET['request_id'])) {
    $sendrequest = "INSERT INTO tbl_donorrequest (request_id, donorrequest_date, user_id) 
                    VALUES ('" . $_GET['request_id'] . "', CURDATE(), '" . $_SESSION['uid'] . "')";
    if ($Conn->query($sendrequest)) {
        echo '<script>alert("Request Sent Successfully"); window.location = "viewrequest.php";</script>';
    } else {
        echo "Failure";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .msg {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            color: red;
            font-family: 'Courier New', Courier, monospace;
            font-size: 30px;
        }
        .card-title {
            color: #d26262;
            font-weight: bold;
        }
        .table th {
            background-color:#605e5e;
            color: white;
        }
        .btn-primary {
            background-color: #5bc0de;
            border-color: #5bc0de;
        }
        .btn-primary:hover {
            background-color: #31b0d5;
            border-color: #31b0d5;
        }
        .btn-primary {
    background-color: #d26262;
    border-color: #5bc0de;
}
.header_section {
    background-color: #605e5e !important;
}
.card-title {
    color: #605e5e;
    font-weight: bold;
}
.card {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, .125);
    border-radius: .25rem;
    width: 40cm;
    margin-left: -5cm;
    height: 17cm;
}
.msg {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    color: #d81616;
    font-family: 'Courier New', Courier, monospace;
    font-size: 87px;
    font-family: none;
    margin-top: 4cm;
}
.btn-primary {
    background-color: #2bac99;
    border-color: #2bac99;
}
    </style>
</head>
<body>

<?php
$sel = "SELECT * FROM tbl_user WHERE user_id=" . $_SESSION["uid"];
$res = $Conn->query($sel);
$check = $res->fetch_assoc();
if ($check["user_weight"] < 50) {
    ?>
    <div class="msg">
        Not Eligible to Accept Request
    </div>
    <?php
} else {
    ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card p-4">
                    <h3 class="card-title mb-4 text-center">View Requests</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">SL. NO</th>
                                    <th scope="col">Attendee Name</th>
                                    <th scope="col">Blood Group</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">District</th>
                                    <th scope="col">Place</th>
                                    <th scope="col">Required Date</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                while ($datauser = $resprofile->fetch_assoc()) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo htmlspecialchars($datauser['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['blood_group']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['type_name']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['district_name']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['place_name']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['request_requireddate']); ?></td>
                                        <td><?php echo htmlspecialchars($datauser['request_quantity']); ?></td>
                                        <td>
                                            <?php
                                            $selQry = "SELECT * FROM tbl_donorrequest WHERE user_id='" . $_SESSION['uid'] . "' AND request_id=" . $datauser['request_id'];
                                            $resQry = $Conn->query($selQry);
                                            if ($resQry->num_rows > 0) {
                                                $resData = $resQry->fetch_assoc();
                                                if ($resData['drequest_status'] == 0) {
                                                    echo "Request Sent";
                                                } elseif ($resData['drequest_status'] == 1) {
                                                    echo "Request Accepted";
                                                } elseif ($resData['drequest_status'] == 2) {
                                                    echo "Request Rejected";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if ($resQry->num_rows <= 0) {
                                                ?>
                                                <a href="viewrequest.php?request_id=<?php echo $datauser['request_id']; ?>" class="btn btn-primary btn-sm">
                                                    Send Request
                                                </a>
                                                <?php
                                            } elseif ($datauser['request_status'] == 0) {
                                                ?>
                                                <div>
                                                    <?php echo htmlspecialchars($datauser['user_contact']); ?><br>
                                                    <?php echo htmlspecialchars($datauser['user_address']); ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
