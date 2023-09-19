
<!-- page content -->
<main>
  <section class="page-background">
    <div class="page-content">
      <div class="container-fluid px-4 py-4">
		 <?php $this->load->view('partials/_messages'); ?>
		 
        <div class="row align-items-center">
          <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-0 mb-lg-0">
            <div class="card card-box">
              <div class="card-open-close">
                <div data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" class="open-close-drawer">
                  <div class="card-header border-bottom d-flex align-items-center justify-content-between align-middle">
                    <p class="h4 card-title mb-0">Add image</p>
                    <div class="card-widgets"> <i class="bi bi-chevron-down"></i> </div>
                  </div>
                </div>
              </div>
              <div class="collapse" id="collapseExample">
                <div class="card-body">
                   <form  method="POST" enctype="multipart/form-data" action="<?= base_url();?>upload/uploadimage">
                     <div class="row">
					  <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-lg-2">
                        <label><sup class="text-danger">*</sup>Upload Image</label>
                        <input name="image" class="form-control" type="file" id="image" name="image">
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        
  </div>
  </div>
</section>
</main>
<!-- page content --> 




