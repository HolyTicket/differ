@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="panel-group" id="accordion">

            @foreach($categories as $cat)
                <div class="faqHeader">{{ $cat->name }}</div>
                @foreach($cat->questions as $question)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$question->id}}">{{$question->question}}</a>
                            </h4>
                        </div>
                        <div id="collapse{{$question->id}}" class="panel-collapse collapse">
                            <div class="panel-body">
                                {!! $question->answer !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach


        </div>
    </div>

    <style>
        .faqHeader {
            font-size: 27px;
            margin: 20px;
        }

        .panel-heading [data-toggle="collapse"]:after {
            font-family: FontAwesome;
            content: "\f04b";
            float: right;
            font-size: 18px;
            line-height: 17px;
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            transform: rotate(-90deg);
            color: #908E8E;
        }

        .panel-heading [data-toggle="collapse"].collapsed:after {
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            transform: rotate(90deg);
        }
    </style>
@endsection