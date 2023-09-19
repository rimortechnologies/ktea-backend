<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Valiant Resources | Login</title>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<!-- favicons-->
<link rel="icon" href="<?= base_url();?>assets/images/favicon.png" type="image/x-icon">
<!-- favicons--> 
<!-- css -->
<link type="text/css" href="<?= base_url();?>assets/css/master.css" rel="stylesheet" media="all">
<link type="text/css" href="<?= base_url();?>assets/css/styles.css" rel="stylesheet" media="all">
<!-- css --> 
<!--[if lt IE 9]>
<script src="assets/js/html5shiv.js" type="text/javascript" defer></script>
<script src="assets/js/css3-mediaqueries.js" type="text/javascript" defer></script>
<script src="assets/js/respond.min.js" type="text/javascript" defer></script>
<![endif]--> 
<!-- fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<!-- fonts -->
</head>
<body>
<?= $contents; ?>
<footer>
  <div class="bg-silver py-3">
    <div class="container px-4">
      <div class="row align-items-center">
        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-lg-0 mb-0">
          <p class="text-extra-small font-weight-300">&copy; <span id="current-year"></span> Valiant Resources.</p>
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- js--> 
<script src="<?= base_url();?>assets/js/jquery.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/popper.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/bootstrap.bundle.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/jquery.easing.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/smoothscroll.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/scripts.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/bootstrap-datepicker.min.js" type="text/javascript"></script> 
<!-- datatable --> 
<script src="<?= base_url();?>assets/js/jquery.dataTables.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/dataTables.bootstrap5.min.js" type="text/javascript"></script> 
<script src="<?= base_url();?>assets/js/page-datatable.js" type="text/javascript"></script> 

<script src="<?= base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?= base_url();?>assets/js/additional-methods.min.js"></script>


<script>




$('#reset_password').validate({ 
        rules: {
            password: { required: true,digits: true,
				minlength: 4,
				maxlength: 4,},
			confirm_password: { required: true,digits: true
				minlength: 4,
				maxlength: 4,equalTo: "#password"},
        },
		
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
			return true;
        }
    });
	
	
	
 $('#login').validate({ 
        rules: {
            email: {
                required: true,
				email: true,
            },
            password: {
                required: true,
				minlength: 4,
				maxlength: 4,
				digits: true,
				
            }
        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	</script>
	
<script>
  $(document).ready(function() {
    $("#simple-datatable").DataTable();
    $("input.datepicker").datepicker();
});
</script> 
<!-- datatable --> 
<!-- js-->
</body>
</html>