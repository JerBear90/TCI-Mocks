<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
?>
<?php if( $_GET['json'] ) {
    header('content-type: application/json');
    echo json_encode( $formConfig );
    exit();
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script>
        const formConfig = <?php echo json_encode( getFormConfig() ); ?>
    </script>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
        
    <!-- <script src="js/formConfig.js"></script> -->
    <script>

    </script>
    <link rel="stylesheet" href="<?php echo plugins_url( 'css/font-awesome/font-awesome.min.css', dirname(__FILE__) ); ?>">
    <link rel="stylesheet" href="<?php echo plugins_url( 'css/app.css', dirname(__FILE__) ); ?>">
    <title>Forms</title>
</head>
<body>

    
    <!-- Header -->
	<header class="bg-primary text-white py-3">
		<div class="container">
			<h1>The Ultimate Form Plugin</h1>
		</div>
    </header>

<!-- partial -->
    <section id="main" class="container py-5">
        <div class="row">
            <!-- <div class="card col-md-6">
                <div class="card-header text-center">
                    <h1>PHP Rendered</h1>
                </div>
                <div class="card-body">
                    <?php //$form = new form('test', [ 'debug' => true ] ); ?>
                    <?php //$form->render(); ?>
                </div>
            </div>
            <div class="card col-md-6">
                
                <div class="card-body">
                    <form data-id="test"></form>
                </div>
            </div> -->
            <div class="card-body">
                <div class="card-header text-center">
                    <h1>Existing Forms</h1>
                </div>
                <div class="card-text">
                    <div id="form-editor-list"></div>
                </div>
            </div>
        </div>
        
    </section>
<!-- partial:partial/_footer.html -->

    <footer class="py-3 bg-primary text-white text-center">
        <div class="container">
            <div class="row">
                Created by Steve F
            </div>
    </footer>
   <!-- inject:js -->
   <script src="<?php echo plugins_url( 'js/handlebars.js', dirname(__FILE__) ); ?>"></script>
   <!-- <script src="<?php echo plugins_url( 'js/bundle.js', dirname(__FILE__) ); ?>"></script> -->
   <!-- <script src="<?php echo plugins_url( 'js/scripts.js', dirname(__FILE__) ); ?>"></script> -->
   
   <!-- endinject -->
   <!-- <script id="__bs_script__">//<![CDATA[
        document.write("<script async src='http://brainy:3000/browser-sync/browser-sync-client.js?v=2.26.3'><\/script>".replace("HOST", location.hostname));
    //]]></script> -->
</body>
</html>
<!-- partial -->