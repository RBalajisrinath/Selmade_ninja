<?php
// Database connection details
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'rbs292003sep';
$dbname = getenv('DB_NAME') ?: 'note';

// Flags to track the state of operations
$insert = false;
$update = false;
$delete = false;

// Function to attempt database connection with retries
function connectWithRetry($servername, $username, $password, $dbname, $maxRetries = 5, $delay = 5) {
    $retries = 0;
    while ($retries < $maxRetries) {
        $conn = @mysqli_connect($servername, $username, $password, $dbname);
        if ($conn) {
            return $conn;
        }
        $retries++;
        sleep($delay);
    }
    return false;
}

// Establish a connection to the MySQL database
$conn = connectWithRetry($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    error_log('Connection error: Unable to connect to the database after multiple attempts.');
    die('An error occurred. Please try again later.');
}

// CSRF Token generation and validation
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if a delete request was made
if (isset($_GET['delete'])) {
    $sno = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($sno === false || $sno === null) {
        die('Invalid input');
    }
    
    $stmt = mysqli_prepare($conn, "DELETE FROM `notes` WHERE `notes`.`sno` = ?");
    mysqli_stmt_bind_param($stmt, "i", $sno);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $delete = true;
    }
}

// Check if a POST request was made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }

    // Check if the request is for updating an existing note
    if (isset($_POST['snoEdit'])) {
        $sno = filter_input(INPUT_POST, 'snoEdit', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'titleEdit', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'descriptionEdit', FILTER_SANITIZE_STRING);
        $fingerprint = filter_input(INPUT_POST, 'fingerprint', FILTER_SANITIZE_STRING);

        if ($sno === false || $sno === null || $title === false || $description === false || $fingerprint === false) {
            die('Invalid input');
        }

        $sql = "UPDATE `notes` SET `title` = ?, `description` = ?, `fingerprint` = ? WHERE `notes`.`sno` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $fingerprint, $sno);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $update = true;
        }
    } else {
        // Insert a new note
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $fingerprint = filter_input(INPUT_POST, 'fingerprint', FILTER_SANITIZE_STRING);

        if ($title === false || $description === false || $fingerprint === false) {
            die('Invalid input');
        }

        $currentDate = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `notes` (`title`, `description`, `fingerprint`, `date`) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $fingerprint, $currentDate);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $insert = true;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Note Saver</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Edit Note Modal -->
    <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="ModalLabel">Edit Note</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./index.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="snoEdit" id="snoEdit">
                        <input type="hidden" name="fingerprint" id="fingerprint">
                        <div class="mb-3">
                            <label for="title">Edit Title</label>
                            <input type="text" class="form-control" id="titleEdit" name="titleEdit">
                        </div>
                        <div class="mb-3">
                            <label for="title">Edit Description</label>
                            <textarea class="form-control" id="descEdit" rows="3" name="descriptionEdit"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Note Saver</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Display alerts for insert, update, and delete operations -->
    <?php
    if ($insert) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Your note has been added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($update) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Your note has been updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($delete) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Your note has been deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>

    <!-- Form to add a new note -->
    <div class="container mt-5 my-4 mb-5">
        <form action="./index.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="fingerprint" id="fingerprint-add">
            <div class="mb-3">
                <h2>Add a Note</h2>
                <input type="text" class="form-control" id="Inputtitle" placeholder="Note Title" name="title">
            </div>
            <div class="mb-3">
                <textarea class="form-control" id="note-desc" rows="3" placeholder="Note Description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
    </div>

    <!-- Table displaying the list of notes -->
    <div class="container">
        <table class="table table-striped mt-2 table-hover" id="myTable">
            <thead>
                <tr>
                    <th scope="col">Sno.</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch notes from the database
                $stmt = mysqli_prepare($conn, "SELECT * FROM `notes`");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $i = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $i += 1;
                    echo "
                    <tr>
                    <th scope='row'>" . $i . "</th>
                    <td>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>
                        <button class='edit btn btn-sm btn-primary' id=" . $row['sno'] . ">Edit</button> 
                        <button class='delete btn btn-sm btn-primary mt-1' data-sno='" . $row['sno'] . "'>Delete</button>
                    </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script src="assets/js/script.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eHz" crossorigin="anonymous"></script>
    <script>
        // Initialize FingerprintJS
        FingerprintJS.load().then(fp => {
            fp.get().then(result => {
                document.getElementById('fingerprint').value = result.visitorId;
                document.getElementById('fingerprint-add').value = result.visitorId;
            });
        });

        // Edit button click handler
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', (e) => {
                const sno = e.target.id;
                const title = e.target.closest('tr').children[1].textContent;
                const description = e.target.closest('tr').children[2].textContent;

                document.getElementById('snoEdit').value = sno;
                document.getElementById('titleEdit').value = title;
                document.getElementById('descEdit').value = description;

                new bootstrap.Modal(document.getElementById('editModel')).show();
            });
        });

        // Delete button click handler
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', (e) => {
                if (confirm('Are you sure you want to delete this note?')) {
                    const sno = e.target.getAttribute('data-sno');
                    window.location.href = `./index.php?delete=${sno}`;
                }
            });
        });
    </script>
</body>

</html>
