<!DOCTYPE html>
<html %%_attributes>
  <head>
    <title>%%_title</title>
    <style>
      body {padding: 100px 30px; font: normal 16px Arial; text-align: center}
      h1 {font-size: 1.75em}
      a {color: #216ce4}
      a:hover {color: black}
      svg#icon {margin: 50px 0}
    </style>
  </head>
  <body>
    <h1>%%_title</h1>
    <svg id="icon" width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" style="shape-rendering: auto">
      <defs>
        <linearGradient id="hand_gradient" gradientUnits="userSpaceOnUse" x1="72" y1="72" x2="70" y2="70">
          <stop offset="0" style="stop-color: #f8bc67" />
          <stop offset="1" style="stop-color: #f4a31d" />
        </linearGradient>
      </defs>
      <g id="icon_magnifier">
        <path id="ico_mag-hand" style="fill: url('#hand_gradient')" d="m 74.330078,64.428815 c -2.663817,3.875268 -6.025122,7.236574 -9.90039,9.90039 l 14.623046,14.621094 c 2.733517,2.732934 7.164921,2.732934 9.898438,0 2.732934,-2.733517 2.732934,-7.164921 0,-9.898438 L 74.330078,64.428815 Z"/>
        <ellipse id="ico_mag-lens" cx="43" cy="43" rx="31" ry="31" style="fill: #c7d4e2; fill-opacity: .2; stroke: #c7d4e2; stroke-width: 14px"/>
      </g>
    </svg>
    <p>%%_message</p>
  </body>
</html>