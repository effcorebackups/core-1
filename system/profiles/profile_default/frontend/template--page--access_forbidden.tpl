<!DOCTYPE html>
<html %%_attributes>
    <head>
        <title>%%_title</title>
        <style>
            body {
              padding: 100px 30px;
              font: normal 16px Arial;
              text-align: center;
              color: %%_color__text;
              background: %%_color__page}
            pre {
              display: inline-block;
              max-width: 40em;
              overflow: scroll;
              margin: 100px auto;
              text-align: left}
            a       {color: %%_color__link}
            a:hover {color: %%_color__link_active}
            h1 {font-size: 1.6em}
            svg#icon {margin: 50px 0}
        </style>
    </head>
    <body>
        <h1>%%_title</h1>
        <svg id="icon" width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" style="shape-rendering: auto">
            <defs>
                <linearGradient id="body_gradient" gradientUnits="userSpaceOnUse" x1="0" y1="46" x2="0" y2="96">
                    <stop offset="0" style="stop-color: #f8bc67"/>
                    <stop offset="1" style="stop-color: #f4a31d"/>
                </linearGradient>
                <linearGradient id="arc_gradient" gradientUnits="userSpaceOnUse" x1="48" y1="42" x2="48" y2="46">
                    <stop offset="0" style="stop-color: #c7d4e2"/>
                    <stop offset="1" style="stop-color: #97b3d0"/>
                </linearGradient>
            </defs>
            <g id="icon_lock">
                <path id="ico_lck-body" style="fill: url('#body_gradient')" d="m 0,46 96,0 0,50 -96,0 z"/>
                <path id="ico_lck-arc"  style="fill: none; stroke: url('#arc_gradient'); stroke-width: 14px" d="M 18,46 C 18,32 26.042376,18 48,18 69.957624,18 78,32 78,46"/>
                <rect id="ico_lck-line_1" x="5" y="54" width="86" height="5" style="fill: white; fill-opacity: .15"/>
                <rect id="ico_lck-line_2" x="5" y="64" width="86" height="5" style="fill: white; fill-opacity: .15"/>
                <rect id="ico_lck-line_3" x="5" y="74" width="86" height="5" style="fill: white; fill-opacity: .15"/>
                <rect id="ico_lck-line_4" x="5" y="84" width="86" height="5" style="fill: white; fill-opacity: .15"/>
            </g>
        </svg>
        <p>%%_message</p>
        %%_console
    </body>
</html>