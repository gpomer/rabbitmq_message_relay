<!doctype html>

@include('sections.head', ['title' => 'router test tools'])

<body>
    <div class="container">
        <div class="d-flex justify-content-center h-100">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="jumbotron mt-5 pt-1" id="main-jumbo">
                            <h1 class="display-4 headlink"><img src="/images/logo.png" style="width:56px">Routing Test
                                Tools</h1>
                            <div class="container">
                                <div class="row">
                                    <div class="col">

                                        @include('sections.menu')

                                        <hr>
                                        @include('sections.forms.relaymessage')

                                        <hr>
                                        @include('sections.forms.fakeweberror')

                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <hr>
                                    <h5>Service Testing Endpoints</h5>
                                    <p>
                                        You can test the message router from the website, api and admin using the
                                        testrabbitmq url.
                                        This will relay a test message to the message router and you should then be able
                                        to see the
                                        result in the <a href="/logsviewer" target="_blank">logs viewer</a>.
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <hr>
                                    <h5>Code Samples</h5>
                                    <p>
                                        We use RabbitMQ and webhooks to relay messages. Below are code samples
                                        to
                                        show how these are called from a remote machine.
                                    </p>

                                    @include('sections.codesamples')

                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <a href="/logout" class="btn float-right btn login_btn"><span
                                            class="icon-exit"></span> Logout</a>
                                </div>
                            </div>

                            @include('sections.buildspecs')
                        </div>
                    </div>
                </div>

      
            </div>
        </div>
    </div>
    </div>
    @include('sections.foot')
</body>

</html>