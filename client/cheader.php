<!DOCTYPE html>
<html>
    <style>
        .flex-container{
            padding: 0;
            margin: 0;
            list-style: none;
            
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            
            -webkit-flex-flow: row wrap;
            justify-content: space-around;
        }
        
        .my-button{
            background-color:transparent;
            -moz-border-radius:26px;
            -webkit-border-radius:26px;
            border-radius:26px;
            border:2px solid #ffffff;
            display:inline-block;
            curser:pointer;
            color:#ffffff;
            font-size: 0.8em;
            padding:2px 6px;
            text-decoration: bold;
        }
        .my-button:hover{
            background-color:transparent;
        }
        .my-button:active{
            position:relative;
            top:1px;
        }
        .flex-item {
            background: tomato;
            padding: 5px;
            width: 15em;
            height: 10em;
            margin-top: 10px;

            line-height: 1.5em;
            color: white;
            font-weight: bold;
            font-size: 1em;
            text-align: center;
        }

        .flex-header{
            padding: 5px;
            width: 400px;
            height: 100px;
            font-weight: bold;
            font-size: 5em;
            text-align: center;
        }
        .flex-menu{
            padding: 5px;
            width: 10px;
            height: 10px;
            text-align: center;
        }
        .flex-menu2{
            padding: 5px;
            width: 300px;
            height: 200px;
            text-align: center;
        }
    </style>
    <head>
        
    </head>
    <body>
        <div id='customer_navBox'>
        <ul class='flex-container'>
            <li class='flex-menu'>employee?</li>
            <li class='flex-header'>amazam.cam</li>
            <li class='flex-menu'><a href="<?php echo href_link(FILENAME_LOGIN)?>">login</a></li>
            <li class='flex-menu'>register</li>
        </ul>
        </div>
        <div id='customer_content'>



<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

