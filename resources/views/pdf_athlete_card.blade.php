<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Race2.0 - Athlete ID Card</title>
    {{-- <link rel="stylesheet" href="style.css" /> --}}
    <style>
        @font-face {
            font-family: bebas;
            src: url(fonts/Bebas-Regular.ttf);
        }

        @font-face {
            font-family: arial;
            src: url(fonts/arial.ttf);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Bebas", sans-serif;
        }
		
		span{
			font-family: "Bebas", sans-serif;
		}
        .page_break { page-break-before: always; }
		.page { width: 100%; height: 40%; }

    </style>
</head>

<body style="min-height: 180vh;">
    
    {{-- FRONT PART --}}
    <table style="width: 1006px; height: 700px; background-color: #fff; border:1px solid gray; border-radius: 1px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); margin: 20px; padding: 10px 0px 0px 00px;">
        <tr>
            <td style="width: 50%; height: 30px; background-color: white;"></td>
            <td style="width: 50%; height: 30px; background-color: #e31e24;"></td>
        </tr>
        <tr >
            <td colspan="2" style="padding: 20px 20px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 80%;">
                            <h1 style="color: #004394; font-size: 45px; letter-spacing: 5px; "><b>{{ strtoupper($oAthlete[0]->name) }}</b>
                            </h1>
                            {{-- <h1 style="color: #004394; font-size: 65px;">
                                VATSAL NANDU
                            </h1> --}}
                            <h2 style="font-size: 45px; letter-spacing: 4px; color: #e31e24; padding-top: 5px;">
                                {{ $oAthlete[0]->barcode_number }}</h2>
                        </td>
                        <td></td>
                        <td style="width: 20%;">
                            <?php $imagePath = !empty($oAthlete[0]->profile_pic) ? 'uploads/profile_images/'.$oAthlete[0]->profile_pic : 'uploads/images/customer.png'; 
                                    // dd($imagePath);
                            ?>
                            <img src="{{ $imagePath }}" alt=""
                                style="width: 260px; height: 260px; object-fit: cover; border-radius: 0px; margin-top: 0px; float: right;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr><br/><br/><br><br><br><br>
        <tr>
            <td colspan="2" style="padding: 30px 30px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%; ">
                            <img src="uploads/images/img.png" alt="banner_image" style="width: 550px; height: 200px" />
                        </td>
                        <td style="width: 50%">
                            <img src="data:image/png;base64, {!! $oAthlete[0]->barcode_image !!}" style="width: 190px; float: right; margin-top: -10px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; height: 30px; background-color: #004394;"></td>
            <td style="width: 50%; height: 30px; background-color: white;"></td>
        </tr>
    </table>
    {{-- END FRONT PART --}}

    <br><br>
    <div class="page_break"></div>

    {{-- BACK PART --}}
    <table style="width: 1006px; height: 700px; background-color: #fff; border:1px solid gray; border-radius: 1px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); margin: 20px; padding: 0px 10px 0px 10px;">
         <tr>
        <td rowspan="2" style="width: 30px; height: 320px; background-color: #004394;"></td>
        <td style="width: 100%;">
          <h2 style="font-size: 40px; letter-spacing: 0px; color: #e31e24; padding-top: 5px; padding-left: 10px;">IMPORANT INFORMATION:</h2>
        </td>
        <td> <img src="uploads/images/img.png" alt="banner_image32454" style="width: 250px; margin-top: 00px;" /></td>
        <td rowspan="2" style="width: 30px; height: 320px; background-color: #fff;"></td>
      </tr>
      <tr>
        <td colspan="2" rowspan="2" style="vertical-align: top; padding-left: 5px">
          <h4 style="color: #004394;font-size:30px;letter-spacing:1px;padding:5px;">Emergency Contact Name:   {{ !empty($oAthlete[0]->emergency_contact_person) ? ucfirst($oAthlete[0]->emergency_contact_person) : "NA" }}</h4>
          <h4 style="color: #004394;font-size:30px;letter-spacing:1px;padding:5px;">Emergency Contact Number: {{ !empty($oAthlete[0]->emergency_contact_no1) ? ucfirst($oAthlete[0]->emergency_contact_no1) : "NA" }} </h4>
          <h4 style="color: #004394;font-size:30px;letter-spacing:1px;padding:5px;">Blood Group: {{ !empty($oAthlete[0]->blood_group) ? ucfirst($oAthlete[0]->blood_group) : "NA" }}</h4>
          <h4 style="color: #004394;font-size:30px;letter-spacing:1px;padding:5px;">Allergies: {{ !empty($oAthlete[0]->allergies) ? ucfirst($oAthlete[0]->allergies) : "NA" }}</h4>
          <h4 style="color: #004394;font-size:30px;letter-spacing:1px;padding:5px;">Medical Conditions: {{ !empty($oAthlete[0]->medical_conditions) ? ucfirst($oAthlete[0]->medical_conditions) : "NA" }}</h4>
        </td>
      </tr>
      <tr>
        <td rowspan="2" style="width: 30px; height: 320px; background-color: #fff;" ></td>
        <td rowspan="2" style="width: 30px; height: 320px; background-color: #e31e24;" ></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center; padding: 0px 50px;">
          <h3 style="font-size: 18px; color: #004394; text-align: center;"><b>Incase if lost, please deliver it to*</b></h3>

          <h3 style="font-size: 18px; color: #e31e24; text-align: center;">
            <span style="font-weight: bold; font-size:20px;">YouTooCanRun</span>: 3A, Valmiki, Next to Pharmacy College, Behind Kalina Muncipal School, Sunder Nagar, Kalina, Mumbai 400098. Phone: +91 9920142195</h3>
        </td>
      </tr>
    </table>
    {{-- END BACK PART --}}

</body>

</html>

