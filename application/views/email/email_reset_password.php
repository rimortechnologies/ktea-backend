<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RFG - Welcome</title>
  <style type="text/css">
    .ReadMsgBody {
      width: 100%;
      background-color: #FFFFFF;
    }

    .ExternalClass {
      width: 100%;
      background-color: #FFFFFF;
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }

    html {
      width: 100%;
    }

    body {
      -webkit-text-size-adjust: none;
      -ms-text-size-adjust: none;
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica, sans-serif !important;
    }

    table {
      border-spacing: 0;
      table-layout: auto;
      margin: 0 auto;
    }

    img {
      display: block !important;
      overflow: hidden !important;
    }

    .yshortcuts a {
      border-bottom: none !important;
    }

    a {
      color: #aaaaaa;
      text-decoration: none;
    }

    .ava_text_button a {
      font-family: Arial, Helvetica, sans-serif !important;
    }

    .ava_button_link a {
      color: #FFFFFF !important;
    }

    @media only screen and (max-width: 640px) {
      body {
        margin: 0px;
        width: auto !important;
        font-family: Arial, Helvetica, sans-serif;
      }

      .inner_tab {
        width: 90% !important;
        max-width: 90% !important;
      }

      .outer_tab {
        width: 100% !important;
        max-width: 100% !important;
        text-align: center !important;
      }
    }

    /* ===> Responsive CSS For Phones <=== */
    @media only screen and (max-width: 479px) {
      body {
        width: auto !important;
        font-family: Arial, Helvetica, sans-serif;
      }

      .inner_tab {
        width: 90% !important;
        text-align: center !important;
      }

      .outer_tab {
        width: 100% !important;
        max-width: 100% !important;
        text-align: center !important;
      }

      .auto_line {
        width: 100% !important;
        max-width: 100% !important;
        text-align: center !important;
      }
    }
  </style>
</head>

<body>
  <table>
    <tr>

      <td style="padding: 10px 0px;">
        <p style="font-size: .9em;">Hi <?php echo $info['type'] ?>,</p>
      </td>
    </tr>
    <tr>
      <td>
        <p>Password Reset for your Rajah Tea portal.To reset your password click the link below.</p>
      </td>
    </tr>

    <tr style="padding: 50px;">
      <td valign="center" style="text-align: center;">
        <div style="padding: 50px;">
          <a href="<?= base_url(); ?>v1/reset-password?user=<?= $info['type']; ?>&token=<?php echo $info['token']; ?>" style="background-color: #2b90d9;color:#ffffff;padding: 15px 20px;border-radius: 2px;box-shadow: 0px 3px 5px 1px rgba(0,0,0,0.2);margin: auto;">Reset Now</a>
        </div>
      </td>
    </tr>

    <tr>
      <td>
        <table>
          <tr>
            <p>For more information contact us at</p>
            <p class="small_text">enquiry@rajahtea.in</p>
            <p class="small_text">+65 84341786</p>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0"> -->


  <!-- <table class="full" bgcolor="#FFFFFF" align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" bgcolor="#8BC34A" style="background: #00d800;"><table class="inner_tab" width="600" style="max-width: 600px;" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="70"></td>
          </tr>
          <tr>
            <td><table width="100%" align="left" class="outer_tab" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td  dir="ltr" style="font-family: Arial, Helvetica, sans-serif!important; font-size: 18px; font-weight: 500; color: #ffffff; letter-spacing: 0.5px; line-height: 25px;"> Please click on the button below to reset your password. </td>
                </tr>
                <tr>
                  <td height="10"></td>
                </tr>
                <tr>
                  <td  dir="ltr" style="font-family: Arial, Helvetica, sans-serif!important; font-size: 30px; font-weight: 800; color: #ffffff; letter-spacing: 3px; line-height: 33px;"><a href="<?= base_url(); ?>v1/<?= $info['type']; ?>-reset-password?token=<?php echo $info['token']; ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: #000000;color: #ffffff !important; border-radius: 3px;'>
                                       Reset Password
                                    </a></td>
                </tr>
              </table>
              <table width="18" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="7"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="70"></td>
          </tr>
        </table></td>
    </tr>
  </table> -->





  <!-- </table> -->
</body>

</html>