@extends('layouts.email')

@section('content')
    <table border="0" cellpadding="20" cellspacing="0" width="100%">
        <tbody>
        <tr>
            <td valign="top" class="bodyContent" style="border-collapse: collapse;background-color: #FFFFFF;">
                <div mc:edit="std_content00" style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;text-align: left;">
                    <h1 class="h1" style="color: #202020;display: block;font-family: Arial;font-size: 34px;font-weight: bold;line-height: 100%;margin-top: 0;margin-right: 0;margin-bottom: 10px;margin-left: 0;text-align: left;">Welcome to Differ!</h1>

                    <h3 class="h3">Making diffing easy</h3>

                    <p>
                        Hi {{ $name }}!
                    </p>
                    <p>
                        Thanks for registering at Differ! Differ is an online tool, offering:

                    <ul>
                        <li>Storing database connections</li>
                        <li>Show changes between databases</li>
                        <li>Synchronizing databases</li>
                    </ul>
                    </p>
                    <p>
                        You're now able to login at <a href="{{ url('/login') }}">www.differdb.com</a>. Happy Diffing!
                    </p>
                    <p>
                        Best regards,
                    </p>
                    <p>
                        Tom <br />
                        Differdb.com
                    </p>

                </div>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" class="bottomShim" style="border-collapse: collapse;">
                <table border="0" cellpadding="0" cellspacing="0" width="260" class="emailButton" style="background-color: #707070;border-collapse: separate;border-radius: 4px;">
                    <tbody>
                    <tr>
                        <td align="center" valign="middle" class="buttonContent" style="border-collapse: collapse;color: #FFFFFF;font-family: Helvetica;font-size: 18px;font-weight: bold;line-height: 100%;padding: 15px;text-align: center;">
                            <a href="{{ url('/login') }}" style="color: #FFF;text-decoration: none;display: block;" target="_blank">Start diffing!</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

@endsection