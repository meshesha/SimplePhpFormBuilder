<?php
    define('APP_VERSION',"1.3.2");
    define('ABOUT_APP_AUTHOR', preg_replace("/[\r\n]*/","","
    <style>
        .about-row.content {height: 100%}
        footer {
            background-color: #4186f5;
            color: white;
            padding: 5px;
        }
    </style>
    <div class='container-fluid'>
        <div class='row about-row ui-widget-content'>
            <div class='col-sm-9'>
                <h2>About SimplePhpFormBuilder </h2>
                <p>SimplePhpFormBuilder is a PHP Aplication that allowing you to build and management simple html forms.</p>
                <span>Version: " . APP_VERSION . " </span><br>
                <span>Homepage: <a href='https://github.com/meshesha/SimplePhpFormBuilder'>https://github.com/meshesha/SimplePhpFormBuilder</a> </span><br>
                <span>License: <a href='https://github.com/meshesha/SimplePhpFormBuilder/blob/master/LICENSE' target='_blank'><span class='btn btn-primary'>MIT</span></a> </span><br>
                <hr>
                <h2>About author</h2>
                <span>Name: Tady meshesha </span><br>
                <span>Email: meshesha1@gmail.com </span><br>
                <span>Homepage: <a href='https://meshesha.js.org'>https://meshesha.js.org</a> </span><br>
                <span>GitHub: <a href='https://github.com/meshesha'>https://github.com/meshesha</a> </span><br>
            </div>
        </div>
    </div>

    <footer class='container-fluid'>
    <p>SimplePhpFormBuilder by Meshesha</p>
    </footer>")
);
    
