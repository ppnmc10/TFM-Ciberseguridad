<?php

if( isset( $_GET[ 'Login' ] ) ) {
	// Get username
	$user = $_GET[ 'username' ];

	// Get password
	$pass = $_GET[ 'password' ];
	$pass = md5( $pass ); // Mantener el hashing original, aunque md5 no es seguro para contraseñas.

	// Check the database using Prepared Statements
	$query  = "SELECT * FROM `users` WHERE user = ? AND password = ?;";

    // Asumiendo que $GLOBALS["___mysqli_ston"] es el objeto de conexión mysqli
    $mysqli_conn = $GLOBALS["___mysqli_ston"];

    if ($stmt = mysqli_prepare($mysqli_conn, $query)) {
        // Bind parameters
        // "ss" indica que ambos parámetros son strings
        mysqli_stmt_bind_param($stmt, "ss", $user, $pass);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result set
        $result = mysqli_stmt_get_result($stmt);

        if( $result && mysqli_num_rows( $result ) == 1 ) {
            // Get users details
            $row    = mysqli_fetch_assoc( $result );
            $avatar = $row["avatar"];

            // Login successful
            $html .= "<p>Welcome to the password protected area " . htmlspecialchars($user) . "</p>"; // Sanear $user para salida HTML
            $html .= "<img src=\"" . htmlspecialchars($avatar) . "\" />"; // Sanear $avatar para salida HTML
        }
        else {
            // Login failed
            $html .= "<pre><br />Username and/or password incorrect.</pre>";
        }

        // Close the statement
        mysqli_stmt_close($stmt);

    } else {
        // Error preparing statement
        $html .= '<pre>Error al preparar la consulta: ' . ((is_object($mysqli_conn)) ? mysqli_error($mysqli_conn) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>';
    }

	// Close the connection
	((is_null($___mysqli_res = mysqli_close($mysqli_conn))) ? false : $___mysqli_res);
}

?>