@extends('emails.base')

@section('content')
    <div style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;" class="mj-column-per-100" aria-labelledby="mj-column-per-100">
        <table style="background:white;" width="100%">
            <tbody>
            <tr>
                <td style="font-size:0;padding:30px 30px 16px;" align="left">
                    <div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">
                        Please confirm your email address by clicking the link below.
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-size:0;padding:0 30px 6px;" align="left">
                    <div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">
                        We may need to send you critical information about our service and it is
                        important that we have an accurate email address.
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-size:0;padding:8px 16px 10px;padding-bottom:16px;padding-right:30px;padding-left:30px;" align="left">
                    <table cellpadding="0" cellspacing="0" style="border:none;border-radius:25px;" align="left">
                        <tbody>
                        <tr>
                            <td style="background:#00a8ff;border-radius:25px;color:white;cursor:auto;" align="center" valign="middle" bgcolor="#00a8ff">
                                <a class="mj-content" href="{{ url('/verify', ['email' => $email, 'token' => $token]) }}" style="display:inline-block;text-decoration:none;background:#00a8ff;border:1px solid #00a8ff;border-radius:25px;color:white;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;font-weight:400;padding:8px 16px 10px;" target="_blank">
                                    Confirm Email Address
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="font-size:0;padding:0 30px 30px 30px;" align="left">
                    <div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">
                        — Cadabra Express
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
