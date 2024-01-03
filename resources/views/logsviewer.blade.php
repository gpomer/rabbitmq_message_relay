<!doctype html>

@include('sections.head', ['title' => 'logs viewer'])

<body>
  <div class="container">
    <div class="d-flex justify-content-center h-100">

      <div class="container">
        <div class="row">
          <div class="col">

            <div class="jumbotron mt-5 pt-1" id="main-jumbo">
              <h1 class="display-4 headlink"><img src="/images/logo.png" style="width:56px"> Message Router
                Logs
              </h1>
              <div class="container">
                <div class="row">
                  <div class="col">
                    @include('sections.menu')
                    <hr>
                    @if(!empty($message))
                    <div class="alert alert-success" role="alert">
                      {{$message}}
                    </div>
                    @endif

                    @if(!empty($lognames))
                    <div class="form-group">
                      <label for="logSelector">Consumer Logs (order by newest first)</label>
                      <select class="form-control" name="logSelector" id="logSelector">
                        <option value="">Select</option>
                        @foreach($lognames as $lognameoption)
                        <option value="{{$lognameoption}}" @if($logname==$lognameoption) selected @endif>
                          {{$lognameoption}}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif
                    <br>

                    @if(!empty($logs))
                    <div id="logs-container">
                      {!! $logs !!}
                    </div>
                    @endif

                    <hr>

                    <a href="/logout" class="btn float-right btn login_btn"><span class="icon-exit"></span> Logout</a>

                    <a href="/restartconsumer" class="btn float-right btn mr-3">
                      <span class="icon-power-cord"></span> Restart Consumers</a>

                    <a href="/deletelog/{{ $logname }}" class="btn float-right btn mr-3"><span
                        class="icon-trash-o"></span> Delete Current Log File</a>

             

                  </div>
                </div>

                @include('sections.buildspecs')

              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
    @include('sections.foot')
</body>

</html>