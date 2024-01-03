<!doctype html>
@include('sections.head', ['title' => 'message router documentation'])
<body>
  <div class="container">
    <div class="d-flex justify-content-center h-100">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="jumbotron mt-5 pt-1" id="main-jumbo">
              <h1 class="display-4 headlink"><img src="/images/logo.png" style="width:56px"> Message Router
                Documentation
              </h1>
              <div class="container">
                <div class="row">
                  <div class="col">
                    @include('sections.menu')
                    <hr>
                    <p>
                      <img src="/images/docs/relay.png"></p>
                    <p>

                      @php
                      $rabbitmqDomain = str_replace("messagerouter", "rabbitmq", $_SERVER['SERVER_NAME']);
                      @endphp

                      Message brokers help web applications scale. We are using RabbbitMQ as our primary
                      message broker to provide the ability to offload expensive processes
                      asynchronously without impacting the triggering services (admin and api at present). 
                      Messages are stored in <a href="https://{{ $rabbitmqDomain }}" target="blank">RabbitMQ queues</a> until processed by a consumer.
                    </p>
                    <p>
                      We are using a Laravel based consumer to process the messages received by RabbbitMQ. This consumer
                      system also has an option to bypass RabbitMQ (used for testing and local development). <a href="/testingtools">Testing tools and source code examples</a> are provided. 
                    </p>

                      <p class="text-center"><img src="/images/docs/messagerouter.png" width="680" height="400"></p>
                      <hr>
                      <a href="/logout" class="btn float-right btn login_btn"><span class="icon-exit"></span> Logout</a>
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