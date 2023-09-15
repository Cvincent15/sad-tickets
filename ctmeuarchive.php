<?php
session_start();
include 'php/database_connect.php'; // Include your database connection code here

// Check if the user is already logged in
if (!isset($_SESSION['username'])) {
  // Redirect the user to the greeting page if they are not logged in
  header("Location: index.php");
  exit();
}

// Define a function to fetch data from the violation_tickets table
function fetchViolationTickets() {
  global $conn; // Assuming you have a database connection established

  // Write a SQL query to fetch data from the violation_tickets table
  $sql = "SELECT * FROM violation_tickets"; // Modify this query as needed

  // Execute the query
  $result = mysqli_query($conn, $sql);

  // Check if the query was successful
  if ($result) {
    // Initialize an empty array to store the fetched data
    $data = array();

    // Fetch data and store it in the array
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }

    // Return the fetched data
    return $data;
  } else {
    // Handle the error, e.g., display an error message
    echo "Error: " . mysqli_error($conn);
    return array(); // Return an empty array if there was an error
  }
}


// Fetch the violation ticket data
$violationTickets = fetchViolationTickets();

// Check if there are any archived tickets (is_settled = 1)
$hasArchivedData = false;
foreach ($violationTickets as $ticket) {
    if ($ticket['is_settled'] == 1) {
        $hasArchivedData = true;
        break; // No need to continue checking once we find one archived ticket
    }
}
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css">
    <link rel="stylesheet" href="css/style.css"/>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <title>CTMEU Data Hub</title>
</head>
<style>
  .clickable-cell {
    cursor: pointer;
  }

  .hidden {
  display: none;
  margin: auto;
}
#filter-select {
  padding: 10px;
  margin-left: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.search-container {
  text-align: center;
  margin: 20px 0;
}

#search-bar {
  padding: 10px;
  width: 50%;
  border: 1px solid #ccc;
  border-radius: 5px;
}

/* Hide rows that don't match the search term */
.clickable-row {
  display: table-row;
}

.toggle-archive-button {
    display: none;
}

</style>
<body style="height: auto;">
<nav class="navbar navbar-expand-sm navbar-light" style="background-color: #FFFFFF">
  <div class="container-fluid">
  <a class="navbar-brand" href="ctmeupage.php">
  <img src="./images/ctmeusmall.png" class="d-inline-block align-text-middle">
  <span style="color: #1D3DD1; font-weight: bold;">CTMEU</span> <span style="font-weight: 600;"> Data Hub </span>
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="d-flex">
        <ul class="navbar-nav me-2">
          <li class="nav-item">
            <a class="nav-link" href="ctmeupage.php" style="font-weight: 600;">Records</a>
          </li>
          <?php
      // Check if the user role is "IT Administrator"
      if ($_SESSION['role'] === 'IT Administrator') {
          // Do not display the "Create Accounts" link
      } else {
          // Display the "Create Accounts" link
          echo '<li class="nav-item">
          <a class="nav-link" href="ctmeurecords.php" style="font-weight: 600;">Reports</a>
        </li>';
          echo '<li class="nav-item">
          <a class="nav-link" href="ctmeuarchive.php" style="font-weight: 600; color: #1D3DD1;">Archive</a>
        </li>';
      }
      ?>
          <li class="nav-item">
            <a class="nav-link" href="ctmeuticket.php" style="font-weight: 600;">Ticket</a>
          </li>
          <li class="nav-item">
            <!-- <a class="nav-link" href="#">Contact</a> -->
          </li>
        </ul>
        <div class="dropdown-center">
  <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
  <img src="./images/Icon.png" style="margin-right: 10px;"><span id="welcome-text"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="ctmeuusers.php">User Account</a></li>
    <li><a class="dropdown-item" href="ctmeucreate.php">Create Account</a></li>
    <li><a class="dropdown-item" id="logout-button" style="cursor: pointer;">Log Out</a></li>
  </ul>
</div>
    </div>
    </div>
  </div>
</nav>
<div class="search-container">
  <input type="text" id="search-bar" placeholder="Search...">
  <select id="filter-select">
  <option value="name">Name</option>
    <option value="license">License No.</option>
    <option value="vehicle">Vehicle</option>
    <option value="place of occurrence">Place of Occurrence</option>
  </select>
</div>

<div class="table-container">
<?php
// Check if there are violation tickets to display
if ($hasArchivedData) {
    
?>
<table>
    <thead>
        <tr>
            <th class="sortable" data-column="0">No.</th>
            <th class="sortable" data-column="1">Name <span class="sort-arrow"></span></th>
            <th class="sortable" data-column="2">License No. <span class="sort-arrow"></span></th>
            <th class="sortable" data-column="3">Vehicle <span class="sort-arrow"></span></th>
            <th class="sortable" data-column="4">Place of Occurrence <span class="sort-arrow"></span></th>
        </tr>
    </thead>
    <tbody id="ticket-table-body">
        <?php
        $visibleTicketCount = 0; // Initialize a counter for visible tickets

        // Loop through the fetched violation ticket data and populate the table rows
        foreach ($violationTickets as $index => $ticket) {
            // Check if the is_settled value is 1 before making the row clickable
            if ($ticket['is_settled'] == 1) {
                $visibleTicketCount++; // Increment the visible ticket counter
                $emptyResult = true;

                // Convert the row data to a JSON string
                $rowData = json_encode($ticket);

                echo "<tr class='clickable-row' data-index='$index' data-rowdata='$rowData' id='row-$index'>";
                // Display the visible ticket count in the "No." column
                echo "<td>" . $visibleTicketCount . "<input type='hidden' value='" . $ticket['ticket_id'] . "'></td>";
                // Wrap the name in a clickable <td>
                echo "<td class='clickable-cell' data-rowdata='$rowData'>" . $ticket['driver_name'] . "</td>";
                // Wrap the license in a clickable <td>
                echo "<td class='clickable-cell' data-rowdata='$rowData'>" . $ticket['driver_license'] . "</td>";
                // Wrap the address in a clickable <td>
                echo "<td class='clickable-cell' data-rowdata='$rowData'>" . $ticket['vehicle_type'] . "</td>";
                // Wrap the district in a clickable <td>
                echo "<td class='clickable-cell' data-rowdata='$rowData'>" . $ticket['place_of_occurrence'] . "</td>";
                echo "</tr>";
            } else {
                // For rows with is_settled value other than 1, you can choose to display them differently or exclude them from the table.
                // Example: Display a message or simply don't include them in the table.
            }
        }
        
      ?>
    </tbody>
</table>

<?php
} else {
    // Display a card with the message when no archived data is found
    echo '<div class="card" style="text-align:center;"><h1>No archived data yet.</h1></div>';
}
?>
    </div>
<script src="js/script.js"></script>
<script src="js/jquery-3.6.4.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("ticket-table-body");
    const headers = document.querySelectorAll(".sortable");
    let currentColumn = null;
    let isAscending = true;

    // Function to compare two table cells based on their content
    function compareCells(a, b) {
        const aValue = a.textContent || a.innerText;
        const bValue = b.textContent || b.innerText;
        return aValue.localeCompare(bValue, undefined, { numeric: true });
    }

    // Function to sort the table rows
    function sortTable(column, order) {
        const rows = Array.from(tableBody.querySelectorAll("tr"));

        rows.sort((rowA, rowB) => {
            const cellA = rowA.querySelectorAll("td")[column];
            const cellB = rowB.querySelectorAll("td")[column];

            if (order === "asc") {
                return compareCells(cellA, cellB);
            } else {
                return compareCells(cellB, cellA);
            }
        });

        tableBody.innerHTML = "";
        rows.forEach((row) => {
            tableBody.appendChild(row);
        });
    }

    // Function to update the arrow icons
    function updateArrowIcons() {
        headers.forEach((header) => {
            header.classList.remove("asc", "desc");
            const arrowIcon = header.querySelector(".sort-arrow");

            // Check if arrowIcon is not null before updating its innerHTML
            if (arrowIcon) {
                arrowIcon.innerHTML = "";
            }
        });
    }

    // Event listener for header clicks
    headers.forEach((header, index) => {
        header.addEventListener("click", () => {
            const column = parseInt(header.getAttribute("data-column"));

            if (column === currentColumn) {
                // If clicked on the same column, toggle the sorting order
                isAscending = !isAscending;
            } else {
                // If clicked on a different column, set it as the current column and sort in ascending order
                currentColumn = column;
                isAscending = true;
                updateArrowIcons(); // Clear arrow icons in other headers
            }

            // Update the arrow icons and CSS class
            header.classList.add(isAscending ? "asc" : "desc");
            const arrowIcon = header.querySelector(".sort-arrow");
            arrowIcon.innerHTML = isAscending ? "&#9650;" : "&#9660;"; // Unicode symbols for up and down arrows

            // Sort the table
            sortTable(column, isAscending ? "asc" : "desc");
        });
    });
});
</script>

<script>
 // Add a click event listener to the clickable cells
document.querySelectorAll('.clickable-cell').forEach(function(cell) {
    cell.addEventListener('click', function() {
        // Get the row data JSON string from the clicked cell's data-rowdata attribute
        var rowData = cell.getAttribute('data-rowdata');
        
        // Redirect to the details page with the row data as a query parameter
        // Exclude the bx-archive-in button from the row data
        var parsedRowData = JSON.parse(rowData);
        delete parsedRowData.is_settled; // Remove the is_settled property
        window.location.href = 'detailarch.php?data=' + encodeURIComponent(JSON.stringify(parsedRowData));
    });
});
  function rowClick(row) {
    // Get the row data JSON string
    var rowData = row.getAttribute('data-rowdata');
    
    // Redirect to the details page with the row data as a query parameter
    // Exclude the bx-archive-in button from the row data
    var parsedRowData = JSON.parse(rowData);
    delete parsedRowData.is_settled; // Remove the is_settled property
    window.location.href = 'detailarch.php?data=' + encodeURIComponent(JSON.stringify(parsedRowData));
}

  // Add a click event listener to the logout button
document.getElementById('logout-button').addEventListener('click', function() {
        // Perform logout actions here, e.g., clearing session, redirecting to logout.php
        // You can use JavaScript to redirect to the logout.php page.
        window.location.href = 'php/logout.php';
    });

    // Check if the user is logged in and update the welcome message
    <?php if (isset($_SESSION['role']) && isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) { ?>
        var role = '<?php echo $_SESSION['role']; ?>';
        var firstName = '<?php echo $_SESSION['first_name']; ?>';
        var lastName = '<?php echo $_SESSION['last_name']; ?>';

        document.getElementById('welcome-text').textContent = firstName + ' ' + lastName;
    <?php } ?>
    
    function filterTable() {
    var filterSelect = document.getElementById('filter-select');
    var searchInput = document.getElementById('search-bar').value.toLowerCase();

    // Define an object to map filter keys to column names
    var columnMap = {
        'name': 'driver_name',
        'license': 'driver_license',
        'vehicle': 'vehicle_type',
        'place of occurrence': 'place_of_occurrence'
    };

    // Get the column name based on the selected filter key
    var columnName = columnMap[filterSelect.value];

    // Loop through the table rows and filter based on the selected column
    var rows = document.querySelectorAll('#ticket-table-body .clickable-row');
    rows.forEach(function(row) {
        var rowData = JSON.parse(row.getAttribute('data-rowdata'));

        // Get the cell value based on the selected column name
        var cellValue = String(rowData[columnName]).toLowerCase();

        console.log("Search Input: " + searchInput);
        console.log("CellValue: " + cellValue);
        console.log("Filter Key: " + filterSelect.value);
        console.log("Row Data: ", rowData);

        if (cellValue.startsWith(searchInput)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}


    
// Add event listeners to trigger filtering
document.getElementById('filter-select').addEventListener('change', filterTable);
document.getElementById('search-bar').addEventListener('input', filterTable);

</script>

</body>
</html>