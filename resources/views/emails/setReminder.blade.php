@extends('layouts.mail')
@section('content')   
<tr>
   <td align="center" class="sm-px-24" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">
      <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
         <tr>
            <td class="sm-px-24" style="mso-line-height-rule: exactly; border-radius: 4px; background-color: #ffffff; padding: 48px; text-align: left; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 14px; line-height: 24px; color: #626262;">
               <p style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-bottom: 3; font-size: 20px; font-weight: 600;">Dear <span style="font-weight: 700; color: #ff5850;">{{isset($name) ? $name : 'Valued Customer'}}!</span></p>
               <p class="sm-leading-32" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-bottom: 24px; font-size: 20px; font-weight: 600; color: #263238;">
                  🏆 New Reminder on {{env('APP_NAME')}}
               </p>
               <p style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-top: 24px; margin-bottom: 24px;">
                  <span style="font-weight: 600;">You </span>
                  have a reminder set to take {{$content['dosage']}} dosage of {{$content['drug_name']}} {{$content['frequency']}}. You'd receive another mail any minute from now to serve as the first reminder after that, notifications will be sent to you at the particular time set to serve as reminders for you. We're here to help🤩
               </p>
               <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                     <td style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; padding-top: 32px; padding-bottom: 32px;">
                        <div style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; height: 1px; background-color: #eceff1; line-height: 1px;">&zwnj;</div>
                     </td>
                  </tr>
               </table>
               <p style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin: 0; margin-bottom: 16px;">Thanks,
                  <br>{{env('APP_NAME')}}
               </p>
            </td>
         </tr>
         <tr>
            <td style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; height: 20px;"></td>
         </tr>
         {{-- @include('emails.social') --}}
         <tr>
            <td style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; height: 16px;"></td>
         </tr>
      </table>
   </td>
</tr>
@endsection
                  