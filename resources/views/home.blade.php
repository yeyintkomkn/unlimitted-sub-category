@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-primay">Create Category</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <form action="{{url('category/insert')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>CategoryName</label>
                                <input type="text" name="name" required class="form-control" autofocus>
                            </div>
                           <div class="form-group">
                               <input type="radio" checked id="main" name="type" value="main" onclick="toggle_select_box('main')">
                               <label for="main">MainCategory</label>

                               <input type="radio" id="sub" name="type" value="sub" onclick="toggle_select_box('sub')">
                               <label for="sub">SubCategory</label>
                           </div>
                            <div class="form-group" id="select_box" style="display:none">
                                <select name="parent_id" id="parent_id" class="form-control" required>
                                    @foreach($categories as $item)
                                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                                        @endforeach
                                </select>
                            </div>

                            <input type="submit" value="Save" class="btn btn-primary">

                        </form>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="row justify-content-center">
        <div class="col-md-8 jumbotron">
            <div class="card card-primay">
                <div class="card-header">
                    All Category
                </div>
                <div class="card-body" id="show_category">
                    {{--<ul id="level0">--}}
                        {{--<li>AAAA--}}
                        {{--<ul id="level1">--}}
                            {{--<li>1111</li>--}}
                            {{--<li>1111</li>--}}
                            {{--<ul id="level2">--}}
                                {{--<li>aaaa</li>--}}
                                {{--<li>aaaa</li>--}}
                                {{--<li>aaaa</li>--}}
                            {{--</ul>--}}
                        {{--</ul>--}}
                        {{--</li>--}}
                        {{--<li>AAAA</li>--}}
                        {{--<li>AAAA</li>--}}
                        {{--<li>AAAA</li>--}}
                    {{--</ul>--}}

                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('js')
    <script>
        class Node{
            constructor(category){
                this.id=category['id'];
                this.name=category['name'];
                this.level=category['level'];
                this.parent_id=category['parent_id'];
            }
            get_child_node(arr){
                return arr.filter(function(data){
                    return data['parent_id']===this;
                },this.id);
            };
        }

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();
        });

        let load_data=()=>{
            $.ajax({
                type: 'post',
                url: '{{url("api/all_categories")}}',
                cache:false,
                contentType: false,
                processData: false,
                success: function(result) {
                    let categories=JSON.parse(result);
                    //get main categories
                    let main_categories=categories.filter(function(category){
                        return category['level']===0;
                    });
                    show_category(main_categories,categories);
                },
            });
        };


        let show_category=(categories,all_categories)=>{
           if(categories.length!==0){
               let node_obj=new Node(categories[0]);
               presentation_data(node_obj);

               let child_nodes=node_obj.get_child_node(all_categories);
               categories.shift();

               if(child_nodes.length>0){
                   let new_arr=child_nodes.concat(categories);
                   show_category(new_arr,all_categories);
               }
               else if(child_nodes.length===0){
                   show_category(categories,all_categories);
               }
           }
        };

        let presentation_data=(category)=>{
            let show_category=document.getElementById('show_category');
            show_category.innerHTML+="level "+category.level+" => "+category.id+":"+category.name+"<br>";
        };

        let toggle_select_box=(type)=>{
            const select_box=document.getElementById('select_box');
            if(type==='main'){
                select_box.style.display="none";
            }
            else if(type==='sub'){
                select_box.style.display="block";
            }
        };

    </script>

    @endsection