<?php

function render_page()
{
    header("Content-Type: text/html; charset=utf-8");
    include("template.php");
    exit();
}

function get_page_content()
{
    global $content;
    echo $content;
}

function get_page_menu()
{
    $menu = "<nav>
                <a href='#' id='menu-icon'></a>
                <ul>
                    <li><a href='.' class='current'>Home</a></li>
                    <li><a href='synergystats.php'>Synergy Statistics</a></li>
                    <li><a href='trends.php'>Trends</a></li>
                    <li><a href='#'>Contact</a></li>
                </ul>
            </nav>";
    echo $menu;
}