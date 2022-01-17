<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pair Matching Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-5" style="background: linear-gradient(45deg,#620b76 0%,#453776 60%); min-height:100vh">

    <div class="container p-5 bg-light rounded" style="min-height: calc(100vh - 6em);">
        <div id="errors"></div>
        <div class="row">
            <div class="col-md-6">
                <h4>Matching Requirements</h2>
                    <div class="p-4 border border-2 rounded mb-4">
                        <form id="form" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-2">
                                <label for="division_score" class="text-secondary">Division Macth Score</label>
                                <select class="form-select" name="division[score]" id="division_score">
                                    <option value="10">10 %</option>
                                    <option value="20">20 %</option>
                                    <option value="30">30 %</option>
                                    <option value="40" selected>40 %</option>
                                    <option value="50">50 %</option>
                                </select>
                            </div>

                            <div class="form-group mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="age_score" class="text-secondary">Age Macth Score</label>
                                        <select class="form-select" name="age[score]" id="age_score">
                                            <option value="10">10 %</option>
                                            <option value="20">20 %</option>
                                            <option value="30" selected>30 %</option>
                                            <option value="40">40 %</option>
                                            <option value="50">50 %</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="age_range" class="text-secondary">Age Range</label>
                                        <select class="form-select" name="age[range]" id="age_range">
                                            <option value="=">Equal</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5" selected>5</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="timezone_score" class="text-secondary">Timezone Macth Score</label>
                                        <select class="form-select" name="timezone[score]" id="timezone_score">
                                            <option value="10">10 %</option>
                                            <option value="20">20 %</option>
                                            <option value="30" selected>30 %</option>
                                            <option value="40">40 %</option>
                                            <option value="50">50 %</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="timezone_range" class="text-secondary">Timezone Range</label>
                                        <select class="form-select" name="timezone[range]" id="timezone_range">
                                            <option value="=">Equal</option>
                                            <option selected value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="file">Upload CSV</label>
                                        <input type="file" name="file" id="file" class="form-control" accept=".csv" required />
                                    </div>

                                    <div class="col-md-6 d-grid">
                                        <button class="btn btn-primary submit mt-4">
                                            <span class="pre-submit">Submit</span>
                                            <span class="post-submit d-none spinner-border spinner-border-sm"></span>
                                            <span class="post-submit d-none">Loading...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
            </div>
            <div class="col-md-6">
                <h4>Allow uploading a .csv</h4>
                <div class="p-4 border border-2 rounded">
                    <p class="text-secondary">Uploaded files must contain 5 columns and 1 header row. The header row must be ordered as:</p>
                    <ul class="text-secondary">
                        <li>Name</li>
                        <li>Email</li>
                        <li>Division</li>
                        <li>Age</li>
                        <li>UTC offset (number range [-12, 12])</li>
                    </ul>

                </div>
            </div>
        </div>

        <div id="report" class="mt-5"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const preRequest = () => {
            $('.submit').attr('disabled', true);
            $('.pre-submit').addClass('d-none');
            $('.post-submit').removeClass('d-none');
            $('#errors').html('');
            $('#report').html('');
        }

        const postRequest = () => {
            $('.submit').removeAttr('disabled');
            $('.pre-submit').removeClass('d-none');
            $('.post-submit').addClass('d-none');
        }

        const displayErrors = (data) => {
            let html = '';
            for (error of data) {
                html += `<div class="alert alert-danger">${error}</div>`;
            }
            $('#errors').html(html);
        }

        const displayReport = (data) => {
            let html = '';
            if (!data.length) {
                html += `<h1 class="text-danger">No recommended pair matches</h1>`;
                $('#report').html(html);
                return;
            }

            html += `<h1 class="text-success">Recommended pair matches</h1>`;
            html += `<table class="table border border-2">`;

            for (row of data) {
                html += `<tr>`;
                html += `<td>${row['name1']}</td>`;
                html += `<td>${row['name2']}</td>`;
                html += `<td>${row['score']} %</td>`;
                html += `</tr>`;
            }

            $('#report').html(html);
        }

        $('#form').on('submit', (e) => {
            e.preventDefault();

            const formEl = e.target;
            const formData = new FormData(formEl);
            preRequest();
            $.ajax({
                url: '/api/upload',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            }).done((response) => {
                formEl.reset();
                displayReport(response.data);
            }).fail((error) => {
                const errors = error.responseJSON && error.responseJSON.data
                    && error.responseJSON.data.length ?
                    error.responseJSON.data : ['Error! Please try later.'];
                displayErrors(errors);
            }).always(() => {
                postRequest();
            });
        });
    </script>
</body>

</html>
