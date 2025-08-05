
@extends('layouts.app')

@section('title') Início @endsection

@section('content')

<div class="container-fluid" style="background-color:#5C7CB6; font-family:Arial, Helvetica, sans-serif; padding:5px; text-shadow: 1px 1px black; height: 30px; font-weight: bold; color: #fff;">
    <div class="col-12">
    "Olá, seja bem-vindo(a) {{session()->get('usuario.nome')}}"        
       
        <div class= "col mx-auto text-center" style="margin-top: 200px;">
            <img class="img-responsive" src="{{ URL::asset('/images/logo.jpg')}}" width="250">
        </div>
    </div>
</div>



@endsection

