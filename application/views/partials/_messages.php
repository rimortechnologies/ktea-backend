<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<?php if ($this->session->flashdata('errors')): ?>
	
<div class="row">
	<div class="col-lg-12 ">
	
		<div class="alert alert-danger alert-dismissible fade show" role="alert">  <?php echo $this->session->flashdata('errors'); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">  </button>
		</div>
	</div>
 </div>

 
 
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>

<div class="row">
	<div class="col-lg-12 ">
		<div class="alert alert-danger alert-dismissible fade show" role="alert">  <?php echo $this->session->flashdata('error'); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">  </button>
		</div>
	</div>
 </div>

		
<?php elseif ($this->session->flashdata('success')): ?>
<div class="row">
	<div class="col-lg-12 ">
		<div class="alert alert-success alert-dismissible fade show" role="alert">  <?php echo $this->session->flashdata('success'); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
		</div>
	</div>
 </div>

<?php endif; ?>
