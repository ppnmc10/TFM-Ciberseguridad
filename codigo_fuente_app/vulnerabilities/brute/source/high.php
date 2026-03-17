<?php

if( isset( $_GET[ 'Login' ] ) ) {
	// Check Anti-CSRF token
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'index.php' );

	// Get username input
	$user = $_GET[ 'username' ];

	// Get password input and hash it
	$pass = $_GET[ 'password' ];
	$pass = md5( $pass ); // Hash the password before checking against the database

	// Establish connection (assuming $GLOBALS["___mysqli_ston"] is an existing mysqli connection)
	$conn = $GLOBALS["___mysqli_ston"];

	// Check database using Prepared Statements
	// Use placeholders (?) for user-supplied values
	$query  = "SELECT * FROM `users` WHERE user = ? AND password = ?;";
	
	// Prepare the statement
	if ($stmt = mysqli_prepare($conn, $query)) {
		// Bind parameters to the placeholders
		// 'ss' indicates that both parameters are strings
		mysqli_stmt_bind_param($stmt, "ss", $user, $pass);

		// Execute the statement
		mysqli_stmt_execute($stmt);

		// Get the result set from the executed statement
		$result = mysqli_stmt_get_result($stmt);

		if( $result && mysqli_num_rows( $result ) == 1 ) {
			// Get users details
			$row    = mysqli_fetch_assoc( $result );
			$avatar = $row["avatar"];

			// Login successful
			// Consider output escaping (e.g., htmlspecialchars) for $user and $avatar to prevent XSS.
			// This fix focuses specifically on SQL Injection.
			$html .= "<p>Welcome to the password protected area " . htmlspecialchars($user) . "</p>";
			$html .= "<img src=\"" . htmlspecialchars($avatar) . "\" />";
		}
		else {
			// Login failed
			sleep( rand( 0, 3 ) );
			$html .= "<pre><br />Username and/or password incorrect.</pre>";
		}
		
		// Close the statement
		mysqli_stmt_close($stmt);
	} else {
		// Handle error in preparing the statement
		die( '<pre>' . mysqli_error($conn) . '</pre>' );
	}

	// Close the connection
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}

// Generate Anti-CSRF token
generateSessionToken();

?>