


<style>

    .font {

        font-family: Helvetica;
        color: #009bf5;

    }

    .center {

    position: absolute;

    left: 50%;
    transform: translate(-50%, 0);

    }

    .h1 {

        font-size: 20vh;
        font-weight: bolder;
        margin-bottom: -6vh;
        margin-top: none;
    }

    .h2{

        font-size: 5vh;
        font-weight: bold;
        margin-bottom: -2vh;

    }

    .h3{


        font-weight: bold;
        margin-bottom: none;
        font-size: 2.5vh;

    }

    .btn {

        font-family: Helvetica;
        color: white;
        background-color: #009bf5;
        border: none;
        padding: 10px 20px 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        margin-top: 7vh;

    }

    .btn:hover {


        opacity: 80%;
    }

    .btn:active {


        opacity: 60%;

    }



</style>

<body>


    <div class="center">
            <center>
                <p class="font h1">500</p>
                <p class="font h2">ERRO INESPERADO!</p>
                <p class="font h3">Codigo do Erro : {{ $code }}</p>
                <button class="btn"><a href="/login/valida" style="text-decoration: none; color: white; font-family: Helvetica;">Retornar para o Menu Principal</a></button>
            </center>
        </div>

</body>

