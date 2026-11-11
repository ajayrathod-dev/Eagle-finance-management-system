<?php



session_start();

include("connection.php");

$current_time = date("M d, Y H:i:s");



if (isset($_COOKIE['uuid'])) {

	$u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);

} else {

	$u_uname = '';

}



 $sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";

$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows > 0) {

	while ($row4 = $result4->fetch_assoc()) {

		$u_uname = mysqli_real_escape_string($link, $row4['user_id']);

		$br_id = mysqli_real_escape_string($link, $row4['user_id']);

	}

} else {

	header("location:logout.php");

	exit;

}




include('header.php');



?>



<style>

	.blink_me {

		animation: blinker 1s linear infinite;

		color: white;

	}



	@keyframes blinker {

		50% {

			opacity: 0;

		}

	}













.rainbow {

    text-align: center;

    text-decoration: none;

    font-size: 14px;

    font-family: monospace;

    letter-spacing: 0px;

}

.rainbow_text_animated {

    background: linear-gradient(to right, #6666ff, #0099ff , #00ff00, #ff3399, #6666ff);

    -webkit-background-clip: text;

    background-clip: text;

    color: transparent;

    animation: rainbow_animation 6s ease-in-out infinite;

    background-size: 400% 100%;

}



@keyframes rainbow_animation {

    0%,100% {

        background-position: 0 0;

    }



    50% {

        background-position: 100% 0;

    }

}




 
</style>

 
<div class="page-wrapper">
			<div class="page-content">
			
			<div class="row">
					<div class="col-xl-12 mx-auto">
						
						<div class="card border-top border-0 border-4 border-primary">
							<div class="card-body p-5">
								<div class="card-title d-flex align-items-center">
									<div><i class="bx bxs-user me-1 font-22 text-primary"></i>
									</div>
									<h5 class="mb-0 text-primary">Create New Location Code</h5>
								</div>
								<hr>
								<form class="row g-3" action="add_new_location1.php" method="post">
									<div class="col-md-6">
										<label for="inputFirstName" class="form-label">Location Code</label>
										<input type="text" class="form-control" id="inputFirstName" name="loc_code" required>
									</div>
									
									
									<div class="col-md-6">
										<label for="inputEmail" class="form-label">Rack Code</label>
           								<input type="text" class="form-control" id="inputLastName" name="rack_code" required>

									</div>
									
										
									<div class="col-md-6">
										<label for="inputEmail" class="form-label">Shelf Code</label>
           								<input type="text" class="form-control" id="inputLastName" name="shelf_code" required>

									</div>
									
										<div class="col-md-6">
										<label for="inputEmail" class="form-label">Description</label>
           								<input type="text" class="form-control" id="inputLastName" name="description" required>

									</div>
									
																
									<div class="col-12" align="center">
										<button type="submit" class="btn btn-outline-primary px-5">Create New Location</button>
									</div>
								</form>
							</div>
						</div>
						
					</div>
				</div>

				 <div class="col-lg-12">
<div class="alert alert-dark border-0 bg-dark alert-dismissible fade show" style="height:60px;">
                           <div class="text-white"><h5 class="text-white">Location History</h5></div>
                           
                        </div>
                     </div>

      <div class="card">
         <div class="card-body">
            <div class="table-responsive">
               <table id="example2" class="table table-striped table-bordered">
                  <thead>
                   <tr>
                     <th>Sr.No</th>
                     <th>Location Code</th>
                     <th>Rack Code</th>
                     <th>Shelf Code</th>
                     <th>Description</th>
                     <th>Info</th>
                     <th>By</th>
                     <th>Date</th>
                    

                   </tr>
                 </thead>
                 <tbody>
                   <?php

                     //$table_name = date("Y_m_d", time() + 86400)."_doppler_data";

                    $sr = 1;
                    $sql1 = "SELECT * FROM add_new_location order by id DESC";
                    $result1 = mysqli_query($link, $sql1);
                    if ($result1->num_rows > 0) {
                      while ($usr1 = mysqli_fetch_array($result1)) {
                    ?>
                       <tr>
                         <td><?php echo $sr; ?></td>
                        
                         <td><?php echo $usr1['location_code']; ?></td>
                         <td><?php echo $usr1['rack_code']; ?></td>
                         <td><?php echo $usr1['shelf_code']; ?></td>
                         <td><?php echo $usr1['description']; ?></td>
                         <td><?php echo $usr1['location_code_rack_code_shelf_code']; ?></td>
                         <td><?php echo $usr1['by']; ?></td>
                         <td><?php echo $usr1['date']; ?></td>
                       </tr>

                   <?php
                        $sr++;
                      }
                    }
                    ?>
                 </tbody>

               </table>
            </div>
         </div>
      </div>

			

			</div>
		</div>






<?php

include('footer.php');

?>