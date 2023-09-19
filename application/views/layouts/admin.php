<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Valiant Resources | CRM</title>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<!-- favicons-->
<link rel="icon" href="<?= base_url();?>assets/images/favicon.png" type="image/x-icon">
<!-- favicons--> 
<!-- css -->
<link type="text/css" href="<?= base_url();?>assets/css/master.css" rel="stylesheet" media="all">
<link type="text/css" href="<?= base_url();?>assets/css/styles.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?= base_url();?>assets/css/jquery.fancybox.min.css" type="text/css">	
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
<!-- header -->
<header>
  <div class="py-3 border-bottom">
    <div class="container-fluid px-4">
      <div class="row align-items-center">
        <div class="col mb-0 mb-lg-0">
          <div> <a class="navbar-brand" href="javascript:void(0)"><img src="<?= base_url();?>assets/images/logo-dark.png" class="img-fluid" width="100"></a> </div>
        </div>
        <div class="col mb-0 mb-lg-0">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-5">
			<?php 
			$unreadNotification = unread_notifiation();
			
			?>
              <div class="notification-bell"><a href="<?= base_url();?>notification" title="Notification" class="notification-bell"><span class="d-inline-block fs-20 position-relative"><i class="bi bi-bell"></i></span><span class="badge bg-pink roboto-font counter"><?= $unreadNotification; ?></span></a></div>
            </div>
            <div>
              <div class="dropdown profile-dropdown text-end"> <a href="javascript:void(0)" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false"> <img src="<?= base_url();?>uploads/users/<?= (user()->image?user()->image:'default.png'); ?>" alt="Valiant Resources" width="32" height="32" class="rounded-circle"> </a>
                <ul class="dropdown-menu animate__animated animate__fadeIn" aria-labelledby="dropdownUser1">
                  <div class="d-flex flex-column align-items-center border-bottom p-3">
                    <div class="mb-2"> <img class="rounded-circle" src="<?= base_url();?>uploads/users/<?= (user()->image?user()->image:'default.png'); ?>" alt="Valiant Resources" width="40"> </div>
                    <div class="text-center">
                      <p class="text-extra-small text-indigo roboto-font letter-spacing-1 font-weight-500 mb-2"><span><?= user()->first_name; ?></span> <span><?= user()->last_name; ?></span> </p>
                      <p class="text-tiny font-weight-500 roboto-font text-muted"><?= user()->email; ?></p>
                    </div>
                  </div>
                  <li><a class="dropdown-item border-bottom" href="<?= base_url();?>myprofile">My Profile</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#logoutmodal">Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-dark bg-silver shadow-sm py-2">
    <div class="container-fluid"> <span class="navbar-brand d-xxl-none d-xl-none d-lg-none d-md-block d-sm-block d-xs-block">Navigate</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation"> <span><i class="bi bi-list"></i></span> </button>
      <div class="collapse navbar-collapse" id="navbars">
        <ul class="navbar-nav me-auto py-2 py-lg-0">
          <li class="nav-item"><a class="nav-link active" href="<?= base_url();?>dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>users">Users</a></li>
          <!--<li class="nav-item"><a class="nav-link" href="<?= base_url();?>main-clients">Clients</a></li>-->
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>recruiter">Recruiter</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>sales">Sales</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>data-collector">Data Collector</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>human-resource">Human Resource</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>finance">Finance</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>reports/xUxxvvc=/xUxxvvc=/xUxxvvc=/xUxxvvc=">Reports</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url();?>notification">Notification</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<!-- header --> 
<?= $contents; ?>
<!-- footer -->
<footer>
  <div class="bg-brand-1 py-3">
    <div class="container-fluid px-4">
      <div class="row align-items-center">
        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-lg-0 mb-0">
          <p class="text-tiny text-white font-weight-500">&copy; <span id="current-year"></span> Valiant Resources.</p>
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- footer --> 
<!-- logout -->
<section class="customized-modal">
  <div class="modal fade" id="logoutmodal"  tabindex="-1" aria-labelledby="logoutmodal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <p class="h6 modal-title">Logout</p>
          <button type="button" class="close-button" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="py-3">
                <p class="h5 font-weight-300">Are you sure you want to logout?</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-brand btn-sm" data-bs-dismiss="modal">Stay Connected</button>
          
		  <a class="btn btn-brand-outline btn-sm" href="<?= base_url();?>logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- logout --> 



<!-- logout -->
<section class="customized-modal">
  <div class="modal fade" id="deletemodal"  tabindex="-1" aria-labelledby="deletemodal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <p class="h6 modal-title">Delete</p>
          <button type="button" class="close-button" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="py-3">
                <p class="h5 font-weight-300">Are you sure you want to delete?</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-brand btn-sm" data-bs-dismiss="modal">Cancel</button>
           <span id= 'deleteButton'></span></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- logout --> 



<!-- logout -->
<section class="customized-modal">
  <div class="modal fade" id="activeinactivemodal"  tabindex="-1" aria-labelledby="activeinactivemodal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <p class="h6 modal-title">Delete</p>
          <button type="button" class="close-button" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="py-3">
                <p class="h5 font-weight-300">Are you sure you want to Active / Inactive?</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-brand btn-sm" data-bs-dismiss="modal">Cancel</button>
           <span id= 'activeinactiveButton'></span></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- logout --> 



<!-- logout -->
<section class="customized-modal">
  <div class="modal fade" id="deletenotificationmodal"  tabindex="-1" aria-labelledby="deletenotificationmodal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <p class="h6 modal-title">Delete</p>
          <button type="button" class="close-button" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
        <div class="modal-body">
          <div class="row align-items-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="py-3">
                <p class="h5 font-weight-300">Are you sure you want to delete?</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-brand btn-sm" data-bs-dismiss="modal">Cancel</button>
           <span id= 'deleteNotifiationButton'></span></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- logout --> 




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
<script src="<?= base_url();?>assets/js/ckeditor.js"></script>

<script type="text/javascript" src="<?= base_url();?>assets/js/jquery.fancybox.min.js"></script>
<script>



	 
	
	
	
	
CKEDITOR.replace( 'editor' );

$("#interview_date_div").hide();
$("#interview_time_div").hide();
$("#mode_of_interview_div").hide();
$("#start_date_div").hide();
$("#offered_salary_div").hide();
$("#signed_offer_letter_div").hide();




$('#insert_tickets').validate({ 
        rules: {
			user_to:{required:true,},
			subject:{required:true,},
			message:{required:true,},
		
			
        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });


$('#update_status').validate({ 
        rules: {
			cv_status:{required:true,},
			status_id:{required:true,},
			extra_notes:{required:true,},
			interview_date:{required:true,},
			mode_of_interview:{required:true,},
			interview_time:{required:true,},
			candidate_cv_status:{required:true,},
			signed_offer_letter:{extension: "pdf",required:true,},
			start_date:{required:true,},
			offered_salary:{required:true,},
			
			
			
        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	
 $('#insert_cv').validate({ 
        rules: {
			dt:{required:true,},
			client:{required:true,},
			client_name:{required:true,},
			position:{required:true,},
			candidate:{required:true,},
			candidate_name:{required:true,},
			candidate_ic:{required:true,},
			candidate_phone_number:{required:true,digits: true},
			candidate_email: {required: true,email: true, },
			current_salary:{required:true,},
			expected_salary:{required:true,},
			joining_time:{required:true,},
			buy_out:{required:true,},
			candidate_cv_status:{required:true,},
			notes:{required:true,},


        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	

 $('#insert_position').validate({ 
        rules: {
			
			experience: {required: true, },
			experience_to: {required: true, },
			client: {required: true, },
			position:{required:true,},
			technology:{required:true,},
			race:{required:true,},
			job_type:{required:true,},
			position_no:{required:true,digits: true,},
			status:{required:true,},
			salary_budget:{required:true,},
			salary_budget_to:{required:true,},
			job_description:{extension: "pdf",required:true,},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	$('#update_position').validate({ 
        rules: {
			
			experience: {required: true, },
			experience_to: {required: true, },
			technology:{required:true,},
			salary_budget:{required:true,},
			salary_budget_to:{required:true,},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });

	
$('#insert_data_collector').validate({ 
        rules: {
			position_no:{required:true,digits: true,},
			experience: {required: true, },
			position:{required:true,},
			technology:{required:true,},
			job_type:{required:true,},
			dt:{required:true,},
			client_name:{required:true,},
			contact_person:{required:true,},
			phone_number:{required:true,digits: true},
			email: {required: true,email: true, },
			website:{required:true,url: true},
			address_1:{required:true,},
			address_2:{required:true,},
			city:{required:true,},
			state:{required:true,},
			country:{required:true,},
			postal:{required:true,digits: true},
			
        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	


 $('#update_client').validate({ 
        rules: {
			
			client_name:{required:true,},
			dt:{required:true,},
			contact_person:{required:true,},
			phone_number:{required:true,digits: true},
			email: {required: true,email: true, },
			website:{required:true,url: true},
			alt_phone_number:{required:true,digits: true},
			alt_email: {required: true,email: true, },
			address_1:{required:true,},
			address_2:{required:true,},
			city:{required:true,},
			state:{required:true,},
			country:{required:true,},
			postal:{required:true,digits: true},
			recruitment_fee:{required:true,},
			agreement:{extension: "pdf",},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	


 $('#insert_client').validate({ 
        rules: {
			
			client_name:{required:true,},
			dt:{required:true,},
			contact_person:{required:true,},
			phone_number:{required:true,digits: true},
			email: {required: true,email: true, },
			website:{required:true,url: true},
			alt_phone_number:{required:true,digits: true},
			alt_email: {required: true,email: true, },
			address_1:{required:true,},
			address_2:{required:true,},
			city:{required:true,},
			state:{required:true,},
			country:{required:true,},
			postal:{required:true,digits: true},
			recruitment_fee:{required:true,},
			agreement:{extension: "pdf",required:true,},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	


	
	
	
	
 $('#update_candidate').validate({ 
        rules: {
			email: {required: true,email: true, },
			name:{required:true,},
			ic:{required:true,},
			phone_number:{required:true,digits: true},
			postal:{required:true,digits: true},
			position:{required:true,},
			technology:{required:true,},
			experience:{required:true,},
			current_salary:{required:true,},
			expected_salary:{required:true,},
			experience:{required:true,},
			buy_out:{required:true,},
			job_type:{required:true,},
			address_1:{required:true,},
			address_2:{required:true,},
			city:{required:true,},
			state:{required:true,},
			country:{required:true,},
			cv:{extension: "pdf",},
			notes:{required:true,},
			
			

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	


 $('#insert_candidate').validate({ 
        rules: {
			email: {required: true,email: true, },
			name:{required:true,},
			ic:{required:true,},
			phone_number:{required:true,digits: true},
			postal:{required:true,digits: true},
			position:{required:true,},
			technology:{required:true,},
			experience:{required:true,},
			current_salary:{required:true,},
			expected_salary:{required:true,},
			experience:{required:true,},
			buy_out:{required:true,},
			job_type:{required:true,},
			address_1:{required:true,},
			address_2:{required:true,},
			city:{required:true,},
			state:{required:true,},
			country:{required:true,},
			cv:{required:true,extension: "pdf",},
			notes:{required:true,},
			
			

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	

 $('#update_user').validate({ 
        rules: {
			display_email: {required: true,email: true, },
			role:{required:true,},
			first_name:{required:true,},
			last_name:{required:true,},
			phone_number:{required:true,digits: true},
			country:{required:true,},
			permissions:{required:true,},
			status:{required:true,},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });


 $('#insert_user').validate({ 
        rules: {
            email: {required: true,email: true, },
			display_email: {required: true,email: true, },
			role:{required:true,},
			first_name:{required:true,},
			last_name:{required:true,},
			phone_number:{required:true,digits: true},
			country:{required:true,},
			status:{required:true,},
			permissions:{required:true,},
			password: {required: true,minlength: 4,maxlength: 4},
			confirm_password: {required: true,equalTo: "#password"},
		

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	$('#verify_client').validate({ 
        rules: {
			intrested:{required:true,},
			recruitment_fees:{required:true,},

        },
        errorPlacement: function(){
            return false;
        },
        submitHandler: function (form) { 
		   return true;
        }
    });
	
	$('#update_profile').validate({ 
        rules: {
			old_password: { required: true,digits: true,minlength: 4,maxlength: 4,},
            password: { required: true,digits: true,minlength: 4,maxlength: 4,},
			confirm_password: { required: true,digits: true,minlength: 4,maxlength: 4,equalTo: "#password"},
			image:{ extension: "gif|jpg|png|jpeg|svg",},
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
	  
	  
	$("#intrested").on('change',function(){
		if(this.value=='No')$("#reason_div").show();
		else $("#reason_div").hide();
	
	});




    $("#simple-datatable").DataTable();
    $("input.datepicker").datepicker();
});
</script> 
<!-- datatable --> 
<!-- charts --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script> 
<!--<script src="<?= base_url();?>assets/chart/dashboard-chart.js" type="text/javascript"></script> -->


<script>

//Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Roboto,sans-serif';
Chart.defaults.global.defaultFontColor = '#999999';


<?php if($title=='Dashboard'):?>


<?php 
			  
						$where_table_sum = NULL;
						$position_no = get_table_sum('position','position_no',$where_table_sum);
						
						
						$where_clients_count = NULL;
						$clients_count  = get_table_count('clients',$where_clients_count);
						
			  
						$where_lead_count = NULL;
						$lead_count  = get_table_count('data_collector',$where_lead_count);
			  
						$where_candidates_count = NULL;
						$candidates_count  = get_table_count('candidates',$where_candidates_count);
			  
			  
			  
			  
			  
						$where_candidate_cv_submission = NULL;
						$candidate_cv_submission  = get_table_count('candidate_cv',$where_candidate_cv_submission);
						
						
						$where_candidate_cv_interviews = array('cv_status'=>3);
						$candidate_cv_interviews  = get_table_count('candidate_cv',$where_candidate_cv_interviews);
						
						
						$where_candidate_cv_offers_made = array('cv_status'=>4);
						$candidate_cv_offers_made  = get_table_count('candidate_cv',$where_candidate_cv_offers_made);
						
						$where_candidate_cv_rejected = array('cv_status'=>5);
						$candidate_cv_rejected  = get_table_count('candidate_cv',$where_candidate_cv_rejected);
						
						
						$where_tickets = NULL;
						$tickets_count  = get_table_count('tickets',$where_tickets);
						
						
						
						?>
// dashboard
var ctx = document.getElementById("overall");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Leads", "Clients", "Open Positions", "Open Candidates", "Submissions", "Interviews", "Offers Made", "Rejected/Dropped"],
    datasets: [{
      label: "Total",
      backgroundColor: "rgba(0,70,128,1)",
      borderColor: "rgba(0,70,128,1)",
      data: [<?= $lead_count; ?>, <?= $clients_count; ?>, <?= $position_no->position_no;?>, <?= $candidates_count; ?>, <?= $candidate_cv_submission; ?>, <?= $candidate_cv_interviews; ?>, <?= $candidate_cv_offers_made; ?>, <?= $candidate_cv_rejected; ?>],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 11
        }
      }],
      yAxes: [{
        ticks: {
         // min: 0,
         // max: 500,
         // maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});

<?php endif; ?>




</script>





<!--<script src="<?= base_url();?>assets/chart/chart.js" type="text/javascript"></script> -->
<?php 


		$where_table_sum = NULL;
		$position_no = get_table_sum('position','position_no',$where_table_sum);
		
		$where_candidate_cv_submission = NULL;
		$candidate_cv_submission  = get_table_count('candidate_cv',$where_candidate_cv_submission);
						
						
						
		$where_candidate_cv_interviews = array('cv_status'=>3);
		$candidate_cv_interviews  = get_table_count('candidate_cv',$where_candidate_cv_interviews);


		$where_candidate_cv_offers_made = array('cv_status'=>4);
		$candidate_cv_offers_made  = get_table_count('candidate_cv',$where_candidate_cv_offers_made);

		$where_candidate_cv_rejected = array('cv_status'=>5);
		$candidate_cv_rejected  = get_table_count('candidate_cv',$where_candidate_cv_rejected);
		
		
						$where_clients_count = NULL;
						$clients_count  = get_table_count('clients',$where_clients_count);
						
			  
						$where_lead_count = NULL;
						$lead_count  = get_table_count('data_collector',$where_lead_count);			
						
						?>

<script>
var ctx = document.getElementById("myPieChart1");
var myPieChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Leads", "Clients"],
    datasets: [{
      data: [<?= $lead_count;?>, <?= $clients_count; ?>],
      backgroundColor: ['#5a48b2', '#f6329f'],
    }],
  },
});



// chart 2
var ctx = document.getElementById("myPieChart2");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Interviews", "Offers Made", "Rejected/Dropped"],
    datasets: [{
      data: [<?= $candidate_cv_interviews; ?>, <?= $candidate_cv_offers_made; ?>, <?= $candidate_cv_rejected; ?>],
      backgroundColor: ['#006ed6', '#7cd000', '#ff2636'],
    }],
  },
});

// chart 3
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Open Positions", "Submissions"],
    datasets: [{
      label: "Sales",
      backgroundColor: "rgba(76,79,98,1)",
      borderColor: "rgba(76,79,98,1)",
      data: [<?= $position_no->position_no;?>, <?= $candidate_cv_submission; ?>],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 500,
          maxTicksLimit: 10
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});
function deleteTable(id,table){
	var myModal = new bootstrap.Modal(document.getElementById('deletemodal'));
	onclick_text = "tableDelete('"+table+"',"+id+")";
	$('#deleteButton').html('<a class="btn btn-brand-outline btn-sm" onclick='+onclick_text+'>Delete</a>');
	myModal.show();
}
function tableDelete(table,id){
	$(location).attr('href', '<?= base_url();?>delete_row/'+table+'/'+id);
}

function activeInactiveUser(id){
	var myModal = new bootstrap.Modal(document.getElementById('activeinactivemodal'));
	$('#activeinactiveButton').html('<a class="btn btn-brand-outline btn-sm" onclick="activateInactiveUser('+id+')">Activate/Inactive</a>');
	myModal.show();
} 

function activateInactiveUser(id){
		$(location).attr('href', '<?= base_url();?>status_change_user/'+id)
} 

function activeInactiveClient(id){
	var myModal = new bootstrap.Modal(document.getElementById('activeinactivemodal'));
	$('#activeinactiveButton').html('<a class="btn btn-brand-outline btn-sm" onclick="activateInactiveClient('+id+')">Activate/Inactive</a>');
	myModal.show();
} 
function activateInactiveClient(id){
		$(location).attr('href', '<?= base_url();?>status_change_client/'+id)
} 
function deleteNotification(id){
	var myModal = new bootstrap.Modal(document.getElementById('deletenotificationmodal'));
	$('#deleteNotifiationButton').html('<a class="btn btn-brand-outline btn-sm" onclick="NotificationDelete('+id+')">Delete</a>');
	myModal.show();
} 
function NotificationDelete(id){
		$(location).attr('href', '<?= base_url();?>notification-delete/'+id)
}

function updateStatus(id){

$("#status_id")	.val(id);
var myModal = new bootstrap.Modal(document.getElementById('change-status'))
myModal.show();
		
} 

function getTableInfo(table,id){
	
	$.ajax({
     url:'<?=base_url()?>admin/get_json/'+table+'/'+id,
     method: 'post',
     dataType: 'json',
     success: function(response){
       
       if(response){
 
		var name 			= response.name;
		var ic 				= response.ic;
		var phone_number 	= response.phone_number;
		var email 			= response.email;
		var current_salary 	= response.current_salary;
		var expected_salary = response.expected_salary;
		var joining_time 	= response.joining_time;
		var buy_out 		= response.buy_out;
		var cv 				= response.cv;
		
		
		$('#candidate_name').val(name);
		$('#candidate_ic').val(ic);
		$('#candidate_phone_number').val(phone_number);
		$('#candidate_email').val(email);
		$('#current_salary').val(current_salary);
		$('#expected_salary').val(expected_salary);
		$('#joining_time').val(joining_time);
		$('#buy_out').val(buy_out);
		$("#append-cv").attr("data-src",'<?=base_url()?>uploads/candidates/'+cv);
		

	   }
       }
	 });
}




function interviewInfo(id){
	
	if(id==3){
		$("#interview_date_div").show();
		$("#interview_time_div").show();
		$("#mode_of_interview_div").show();
		$("#candidate_cv_status_submission_div").hide();
	} else {
		$("#interview_date_div").hide();
		$("#interview_time_div").hide();
		$("#mode_of_interview_div").hide();
		$("#candidate_cv_status_submission_div").show();		
	}
	
	
	if(id==4){
		$("#start_date_div").show();
		$("#offered_salary_div").show();
		$("#signed_offer_letter_div").show();
		$("#candidate_cv_status_div").hide();	
	}
	else {		
		$("#start_date_div").hide();
		$("#offered_salary_div").hide();
		$("#signed_offer_letter_div").hide();	
		$("#candidate_cv_status_div").show();
	}
	
	
	
}





</script>


<!-- charts--> 
<!-- js-->
</body>
</html>