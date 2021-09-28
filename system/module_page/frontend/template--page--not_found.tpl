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
      h1 {font-size: 1.75em}
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
        <line id="ico_mag-hand" x1="65" y1="65" x2="84" y2="84" style="stroke: url('#hand_gradient'); stroke-width: 14px; stroke-linecap: round"/>
        <ellipse id="ico_mag-lens" cx="43" cy="43" rx="31" ry="31" style="fill: #c7d4e2; fill-opacity: .2; stroke: #c7d4e2; stroke-width: 14px"/>
      </g>
    </svg>
    <p>%%_message</p>
  %%_console
  </body>
</html>