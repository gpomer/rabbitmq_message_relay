<!doctype html>

@include('sections.head', ['title' => 'message router'])

</head>

<body>
  <div class="container">
    <div class="d-flex justify-content-center h-100">
      <div class="container">
        <nav class="navbar navbar-dark bg-transparenet">
          <a class="navbar-brand" href="#">Bunking
          </a>
        </nav>



        <div class="card">
          <div class="card-header">
            <h3>Message Router</h3>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <img src="/images/logo.png" class="mb-2" style="width:90px">
              </div>
              <div class="col-8">
                <form method="POST" action="/login">
                @csrf
                  <div class="input-group form-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input name="username" type="text" class="form-control" placeholder="username">
                  </div>
                  <div class="input-group form-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input name="password" type="password" class="form-control" placeholder="password">
                  </div>
                  <div class="form-group">
                    <input type="submit" value="Login" class="btn float-right login_btn">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</body>
</html>