<?php
  if(isset( $_SESSION['session'])){
    $id =  $_GET['key'];
    $profile = $_GET['owner'];
    if ($profile == 'admin') {
      $profile = 'staff';
    }
    $owner = $query->GetFromTable($site.'.'.$profile, 'e_key', $id, '', '', '');
    ?>
      <div class="left">
        <div class="expandable">
          <div class="expandable_title">Bio-data<span class="toggle toggle_close"></span></div>
          <div class="expandable_content">
            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">First Name</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['first_name'] ?></p>
                  <input class="edit hide" type="text" name="" value="<?php echo $owner['first_name'] ?>" data-table=".<?php $user ?>" data-column="first_name"/>
                </div>
              </div>
              <div class="cell right">
                <h4 class="cell_title">Middle Name</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['middle_name'] ?></p>
                  <input class="edit hide" type="text" name="" value="<?php echo $owner['middle_name'] ?>" data-table=".<?php $user ?>" data-column="middle_name"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">Last Name</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['sur_name'] ?></p>
                  <input class="edit hide" type="text" name="" value="<?php echo $owner['sur_name'] ?>" data-table=".<?php $user ?>" data-column="sur_name"/>
                </div>
              </div>
              <div class="cell right">
                <h4 class="cell_title">Birth Date</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['birth_date'] ?></p>
                  <input class="edit hide" type="date" name="" value="<?php echo $owner['birth_date'] ?>" data-table=".<?php $user ?>" data-column="birth_date"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">Gender</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['gender'] ?></p>
                  <select class="edit hide" data-table=".<?php $user ?>" data-column="gender">
                    <option value="<?php echo ($owner['gender'] != '') ? $owner['gender']:'0'; ?>"><?php echo ($owner['gender'] != '') ? $owner['gender']:'Select Your Gender'; ?></option>
                    <?php
                      $gender = $query->GetColumnFromTable($site.'.gender', '', '', '', '', 'title');
                      foreach ($gender as $key => $value) {
                        ?>
                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php
                      }
                     ?>
                  </select>
                </div>
              </div>
              <div class="cell right">
                <h4 class="cell_title">Marital Status</h4>
                <div class="cell_content editable" >
                  <p><?php echo $owner['marital_status'] ?></p>
                  <select class="edit hide" data-table=".<?php $user ?>" data-column="marital_status">
                    <option value="<?php echo ($owner['marital_status'] != '') ? $owner['marital_status']:'0'; ?>"><?php echo ($owner['marital_status'] != '') ? $owner['marital_status']:'Select Your Marital Status'; ?></option>
                    <?php
                      $marital_status = $query->GetColumnFromTable($site.'.marital_status', '', '', '', '', 'title');
                      foreach ($marital_status as $key => $value) {
                        ?>
                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php
                      }
                     ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="expandable">
          <div class="expandable_title">Origin<span class="toggle toggle_close"></span></div>
          <div class="expandable_content">
            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">Nationality</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['nationality'] ?></p>
                  <select class="edit hide" data-table=".<?php $user ?>" data-column="nationality">
                    <option value="<?php echo ($owner['nationality'] != '') ? $owner['nationality']:'0'; ?>"><?php echo ($owner['nationality'] != '') ? $owner['nationality']:'Select Your Nationality'; ?></option>
                  </select>
                </div>
              </div>
              <div class="cell right">
                <h4 class="cell_title">State</h4>
                <div class="cell_content editable" >
                  <p><?php echo $owner['state'] ?></p>
                  <select class="edit hide" data-table=".<?php $user ?>" data-column="state">
                    <option value="<?php echo ($owner['state'] != '') ? $owner['state']:'0'; ?>"><?php echo ($owner['state'] != '') ? $owner['state']:'Select Your State'; ?></option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">City</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['city'] ?></p>
                  <select class="edit hide" data-table=".<?php $user ?>" data-column="city">
                    <option value="<?php echo ($owner['city'] != '') ? $owner['city']:'0'; ?>"><?php echo ($owner['city'] != '') ? $owner['city']:'Select Your City'; ?></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="expandable">
          <div class="expandable_title">Bio-data<span class="toggle toggle_close"></span></div>
          <div class="expandable_content">
            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">User name</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['user_name'] ?></p>
                  <input class="edit hide" type="text" name="" value="<?php echo $owner['user_name'] ?>" data-table=".<?php $user ?>" data-column="user_name"/>
                </div>
              </div>

              <div class="cell right">
                <h4 class="cell_title">Email</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['email'] ?></p>
                  <input class="edit hide" type="email" name="" value="<?php echo $owner['email'] ?>" data-table=".<?php $user ?>" data-column="email"/>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="cell left">
                <h4 class="cell_title">Phone Number</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['mobile_phone'] ?></p>
                  <input class="edit hide" type="number" name="" value="<?php echo $owner['mobile_phone'] ?>" data-table=".<?php $user ?>" data-column="mobile_phone"/>
                </div>
              </div>

              <div class="cell right">
                <h4 class="cell_title">Address</h4>
                <div class="cell_content editable">
                  <p><?php echo $owner['address'] ?></p>
                  <input class="edit hide" type="text" name="" value="<?php echo $owner['address'] ?>" data-table=".<?php $user ?>" data-column="address"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="right"><br>
        <div class="profileimagediv" data-owner=<?php echo ($id == $_SESSION['session'])? 'me':'admin'; ?>>
          <img src="<?php echo $no_preview; ?>" alt="">
        </div>
        <div class="tab">
          <a class="button_link">Change Password</a>
          <a class="button_link">Change Image</a>
        </div>

        <div class="expandable">
          <div class="expandable_title">Payment<span class="toggle toggle_close"></span></div>
        </div>
      </div>

    <?php
    if ($_SESSION['session'] == 'Global_Admin') {
      ?>
      <div>
        <a class="button_link right_align change_status" data-id=<?php echo $id; ?> data-type=<?php echo $profile; ?> id="<?php echo ($owner['charge'] == '1')? 'remove_admin':'make_admin'; ?>"><?php echo ($owner['charge'] == '1')? 'Remove Admin':'Make Admin'; ?></a>
      </div>
      <?php
    }
  }
?>
