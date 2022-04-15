<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";

$insert = false;
$update = false;
$delete = false;
$empty = false;

$con = mysqli_connect($servername, $username, $password, $database);

if (!$con) {
    echo "connection has been failed due to " . mysqli_connect_error();
}
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $delete = true;
    $sql = "DELETE FROM notes WHERE sno = $sno";
    $result = mysqli_query($con, $sql);

}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["snoEdit"])) {
        //if snoEdit is set somewhere then update records of current sno id
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];
        $sql2 = "UPDATE notes SET title = '$title' ,  description = '$description' WHERE sno = $sno";
        $result = mysqli_query($con, $sql2);
        if (!$result) {} else {
            $update = true;
        }
    } else {
        //else run the insert query
        $title = $_POST["title"];
        $description = $_POST["description"];
        $sql = "INSERT INTO `notes` (`sno`, `title`, `description`) VALUES (NULL, '$title', '$description')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $insert = true;
        } else {
            echo "Query has not been run due to " . mysqli_error($con);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <script src="script.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="link.css">
    <title>K-Notes</title>
</head>

<body>
    <div class="popup-bg" id="popup-bg">
        <div class="box">
            <!-- below line for closing modal using fun -->
            <i class="fas fa-times crossclose" onclick="closemodal()"></i>
            <h3>Edit this note</h3>
            <form action="/Code/Notes/index.php" method="post" onsubmit="return emptyfield()">
                <!-- for adding snoEdit for unique id used for edit the records -->
                <input type="hidden" name="snoEdit" id="snoEdit">
                <div class="maintitle">
                    <label for="title" class="poptitle">Title: </label>
                    <input type="text" class="modaltitle" id="modaltitle" name="titleEdit" />
                </div>
                <div class="maindescription">
                    <label for="description" class="popdesc">Description: </label>
                    <textarea class="modaldesc" name="descriptionEdit" id="modaldesc" cols="30" rows="3"></textarea>
                </div>
                <div class="formbtn">
                    <button type="reset" class="formclose" onclick="formclose()">Exit</button>
                    <button type="submit" class="formsubmit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
    <div class="navbar">
        <div class="wholenav">
            <div class="navl">
                <h3>Keep Notes</h3>
            </div>
            <div class="navr">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php
if ($insert) {
    // this block use for alert after note inserted successfully
    echo "<div class='alert' id='alert'>
      <div class='head'>
        <h5>Your Note has been inserted successfully !</h5>
      </div>
      <div class='cross'>
        <i class='fas fa-2x fa-times' onclick='fun()'></i>
      </div>
    </div>";
}
if ($update) {
    echo "<div class='alert' id='alert'>
      <div class='head'>
        <h5>Your Note has been updated successfully !</h5>
      </div>
      <div class='cross'>
        <i class='fas fa-2x fa-times' onclick='fun()'></i>
      </div>
    </div>";
}
if ($delete) {
    echo "<div class='alertred' id='alertred'>
      <div class='head'>
        <h5>Your Note has been deleted successfully !</h5>
      </div>
      <div class='cross'>
        <i class='fas fa-2x fa-times' style=color:white onclick='funred()'></i>
      </div>
    </div>";
}
?>
    <div class='alertred' id='alertredempty'>
        <div class='head'>
            <h5>Please enter title and description !<h5>
        </div>
        <div class='cross'>
            <i class='fas fa-2x fa-times' style="color: white;" onclick='funred()'></i>
        </div>
    </div>
    <div class="main">
        <h2>Add Notes</h2>
        <div class="addform">
            <form action="/Code/Notes/index.php" method="post" onsubmit="return emptyfield()">
                <label class="title" for="title">Title</label>
                <input type="text" name="title" id="title" placeholder="Enter your title here.."></input>
                <label class="desc" for="description">Description</label>
                <textarea name="description" id="description" cols="30" rows="3"></textarea>
                <button type="submit" class="subbtn">Submit</button>
            </form>
        </div>
    </div>
    <div class="list">
        <table class="table" id="myTable">
            <thead style="background-color:white">
                <tr>
                    <th>S.No</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
// following code use for getting notes from database into tables
$sql = "SELECT * FROM notes";
$result = mysqli_query($con, $sql);
$num = 1;
// line 135 helps to get data 1 by 1
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr class='trow'>
          <th>" . $num . "</th>
          <td>" . $row['title'] . "</td>
          <td>" . $row['description'] . "</td>
          <td><i class='formedit far fa-edit' id=" . $row['sno'] . " onclick='formedit()' style=color:blue></i><i class='formdelete fas fa-trash-alt' id=" . $row['sno'] . " style=color:red></i></td>
          </tr>";
    $num++;
}
?>
            </tbody>
        </table>
    </div>

    <script>
    // all are for closing alert messages
    function fun() {
        document.getElementById("alert").style.display = "none";
    }

    function funred() {
        document.getElementById("alertredempty").style.display = "none";
        document.getElementById("alertred").style.display = "none";

    }
    </script>
    <script>
    // this is for field should not be blank
    function emptyfield() {
        if (document.getElementById("title").value == "")
            if (document.getElementById("description").value == "")) {
        document.getElementById("alertredempty").style.visibility = "visible";
        document.getElementById("alertredempty").style.opacity = "1";
        return false;
    }
    }
    </script>
    <script>
    // this is for field should not be blank for modal
    function emptyfield() {
        if (document.getElementById("modaltitle").value == "")
            if (document.getElementById("modaldesc").value == "")) {
        document.getElementById("alertredempty").style.visibility = "visible";
        document.getElementById("alertredempty").style.opacity = "1";
        return false;
    }
    }
    </script>

    <!-- premade table using following script -->
    <script>
    $(document).ready(function() {
        $("#myTable").DataTable();
    });
    </script>

    <script>
    // all are for closing or opening of modal box
    function closemodal() {
        document.getElementById('popup-bg').style.cssText = "visibility:hidden; opacity:0";
    }

    function formedit() {
        document.getElementById('popup-bg').style.opacity = '1';
        document.getElementById('popup-bg').style.visibility = 'visible';
    }

    function formclose() {
        document.getElementById('popup-bg').style.opacity = '0';
        document.getElementById('popup-bg').style.visibility = 'hidden';

    }
    </script>

    <script>
    // create array for each element of tr
    // add click event listener to each element
    // when click on that edit button use target and parent node to access those tags
    // using functions and innertext method you will get real values
    // use those values in modal form
    // for edit records use button id to get unique sno using this edit records in modalform
    var edits = document.getElementsByClassName('formedit');
    Array.from(edits).forEach((element) => {
        element.addEventListener('click', (e) => {
            tr = e.target.parentNode.parentNode;
            title = tr.getElementsByTagName('td')[0].innerText;
            description = tr.getElementsByTagName('td')[1].innerText;
            console.log(title, description);
            modaltitle.value = title;
            modaldesc.value = description;
            snoEdit.value = e.target.id;
            console.log(e.target.id);
        });
    });

    //following code is for delete records
    var dels = document.getElementsByClassName('formdelete');
    Array.from(dels).forEach((element) => {
        element.addEventListener('click', (e) => {
            sno = e.target.id;
            if (confirm('Are you sure want to remove this note ?')) {
                window.location = `/CODE/Notes/index.php?delete=${sno}`;
            }
        })
    })
    </script>

</body>

</html>