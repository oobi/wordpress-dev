
<?php 
// Get the options and defaults
$options 	= get_option( 'midgard_auth_jwt_settings' ); 
$message 	= 'Login failed';
$title 		= 'Login failed';

// If login successful, overwrite default message
if( is_user_logged_in() ) {
	// get current user name and set message
	$username 	= wp_get_current_user()->user_nicename;
	$message 	= 'You have logged in as ' . $username;
	$title 		= 'Login successful';
}

// if Custom HTML saved, output that
if( isset( $options['custom_html'] ) && !empty( $options['custom_html'] ) ) :

	// decode and output the custom HTML
	$custom_html = htmlspecialchars_decode( $options['custom_html'] );
	printf( $custom_html, $username, $options['custom_css'] );
	

// Custom HTML not set, use default
else : ?>

	<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title><?php echo $title; ?></title>

		<style type="text/css">
			BODY 					{ height:100vh; width:100vw; margin:0; padding:0;
									  background:#FFF; 
									  font-family:arial, sans-serif; }
			.wrapper 				{ position:fixed; top:50%; left:50%; padding:20px 40px; width:30vw; margin:0 auto;
									  background:#FFF; 
									  text-align:center;
									  transform:translate(-50%, -50%); }
			
			/*================================================================================================*/
			/* Small devices ONLY */
			@media (max-width: 768px) {
				H1 					{ font-size:1.2em; }
			}
		</style>

		<?php // Inject custom CSS if available
		if( isset( $options['custom_css'] ) && !empty( $options['custom_css'] ) ) {
			printf( '<style type="text/css">%s</style>',
				$options['custom_css'] );
		} ?>
	</head>
		<body>
			<div class="wrapper">
				<?php // Output message
				printf( '<h1>%s</h1>', $message ); ?>
			</div>
		</body>
	</html>

<?php endif; // end custom_html check ?>