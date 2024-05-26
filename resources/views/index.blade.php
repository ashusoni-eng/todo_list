@extends('layouts.main')

@section('main-section')
<div class="layouts">
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary">PHP - Simple To Do List</h5>
                    <hr />
                    <div class="row addtaskInput">
                        <form class="form-group">
                            <div class="col-12 col-md-8 d-flex flex-column flex-md-row">
                                <input type="text" class="form-control me-2 taskNameInput" id="task" placeholder="Enter task"
                                autofocus autocomplete="off">
                                <input type="submit" class="btn btn-primary me-1" value="Add Task">
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Task</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                            @php
                                $i = 1;
                            @endphp
                            @if ($tasks)                                
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td scope="row">{{ $i }}</td>
                                        <td>{{ $task->task }}</td>
                                        <td>{{ $task->status }}</td>
                                        <td>
                                            <div class="d-flex btns">
                                                @unless ($task->status=="Done")
                                                    <i
                                                    class="bi bi-check2-circle btn btn-success btn-sm me-2 completeBtn" data-id={{ $task->id }}>
                                                    </i>
                                                @endunless
                                                <i 
                                                class="bi bi-x-lg text-danger btn btn-sm btn-danger text-white deleteBtn"
                                                    data-id={{ $task->id }}
                                                >
                                                </i>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            @endif
                        </table>
                    </div>            
                </div>        
            </div>   
            <span class="showAll">Show All</span>     
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {   
        
        let lastRow= $('table tr:last');
        let sn= lastRow.find('td:eq(0)').text();
        sn++;
        
        // Show loader function
        function showLoader() {
            $("#loader .bar").css('display','block');
            $('#loader .bar').css('width', '0').animate({ width: '100%' }, 2000);
        }

        // Hide loader function
        function hideLoader() {
            $("#loader .bar").css('display','none');
        }
        
        //add task function
        $(".addtaskInput form").on('submit', function(e) {
            e.preventDefault();
            let table = $("table tbody");
            let task = $("#task").val();  

            showLoader();

            $.ajax({
                url: '/task/add',
                type: 'post',
                data: {
                    task: task,
                    _token: $("meta[name='csrf-token']").attr('content')
                },
                success: function(data) {
                    hideLoader();
                    if(data.status){
                        let buttons= `<div class="d-flex">                                            
                                    <i class="bi bi-check2-circle btn btn-success btn-sm me-2 completeBtn" data-id="${data.task.id}"></i>
                                    <i class="bi bi-x-lg text-danger btn btn-sm btn-danger text-white deleteBtn" data-id="${data.task.id}"></i>
                                </div>`;
                        table.append(
                        `<tr>
                            <td>${sn++}</td>
                            <td>${data.task.task}</td>
                            <td>${data.task.status}</td>
                            <td>${buttons}</td>
                        </tr>`
                        );
                        $("#task").val('');                        
                        
                    }else{                        
                        console.log("Can't Add, Somthing Went Wrong");
                    }                        
                },
                error: function(){
                    hideLoader();   
                    $(".taskNameInput").attr('data-toggle', 'tooltip')
                          .attr('data-placement', 'right')
                          .attr('title', 'Duplicate Task Name')
                          .tooltip('show');                    
                    setTimeout(function(){
                        $(".taskNameInput").tooltip('dispose');                    
                    },2000);
                    console.log("Request failed for adding task");
                }
            });
            
        });
        
        //delete task
        $("table").on('click','.deleteBtn' ,function() {
            let selectedRow = $(this).closest("tr");
            let taskId= $(this).data('id');                    
            if (confirm("Do You want to delete this task ??")) {
                showLoader();
                $.ajax({
                    url: '/task/delete/'+taskId,
                    type: 'delete',
                    headers:{
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    dataType:'json',
                    success: function(data) {
                        hideLoader();
                        if(data.status){
                            selectedRow.remove();                                
                        }else{
                            console.log("Can't Delete");
                        }
                    },
                    error: function(){
                    hideLoader();
                    console.log("Request failed for delete task");
                }
                });                    
            }            
        });


        //update task
        $('table').on('click', '.completeBtn', function(){
            let selectedRow = $(this).closest("tr");
            let taskId= $(this).data('id');     
            showLoader();
            $.ajax({
                    url: '/task/update/'+taskId,
                    type: 'put',
                    headers:{
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    dataType:'json',
                    success: function(data) {
                        hideLoader();
                        if(data.status){
                            selectedRow.remove();                                
                        }else{
                            console.log("Can't Completed");
                        }
                    },
                    error: function(){
                    hideLoader();
                    console.log("Request failed for update task");
                }
            });     
        })

        //show all task
        $(".showAll").on('click',function(e){
            e.preventDefault();            
            showLoader();
            $.ajax({
                url: "/show_all",
                type:'get',
                success:function(data){
                    $('body').html(data);
                    hideLoader();                                                          
                },
                error:function(){
                    hideLoader();
                    console.log("Failed to show all tasks");
                }
            })                    
        })        
        
    })
</script>
@endsection
