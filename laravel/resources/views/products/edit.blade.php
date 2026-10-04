<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Products - SantriKoding.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background: white">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Les't Shoot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                <li class="nav-item"><a class="btn btn-outline-light ms-2" href="#">Sign In</a></li>
                <li class="nav-item"><a class="btn btn-light ms-2" href="#">Sign Up</a></li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        
                            @csrf
                            @method('PUT')
                            <div class="text-center mb-3">
                            <h3 class="font-weight-bold">EDIT PRODUCT</h3>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">IMAGE</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                            
                                <!-- error message untuk image -->
                                @error('image')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">SERI</label>
                                <input type="text" class="form-control @error('seri') is-invalid @enderror" name="seri" value="{{ old('seri', $product->seri) }}" placeholder="Masukkan Judul Product">
                            
                                <!-- error message untuk title -->
                                @error('seri')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">MERK</label>
                                <select class="form-control @error('merk') is-invalid @enderror" name="merk" placeholder="Masukkan Judul Product">
                                @foreach ([
                                    (object) [
                                        "label" => "samsung",
                                        "value" => "samsung",
                                        ], 
                                        (object) [
                                        "label" => "vivo",
                                        "value" => "vivo",
                                        ], 
                                        (object) [
                                        "label" => "iphone",
                                        "value" => "iphone",
                                        ],
                                        (object) [
                                        "label" => "oppo",
                                        "value" => "oppo",
                                        ], 
                                    ] as $item )
                                         <option value="{{ $item->value }}">{{ $item->label }}</option>
                                @endforeach
                                </select>
                                <!-- error message untuk title -->
                                @error('merk')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">SISTEM</label>
                                <select class="form-control @error('sistem') is-invalid @enderror" name="sistem" placeholder="Masukkan Judul Product">
                                @foreach ([
                                    (object) [
                                        "label" => "android",
                                        "value" => "android",
                                        ], 
                                        (object) [
                                        "label" => "ios",
                                        "value" => "ios",
                                        ], 
                                    ] as $item )
                                         <option value="{{ $item->value }}">{{ $item->label }}</option>
                                @endforeach
                                </select>
                                <!-- error message untuk title -->
                                @error('sistem')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">UKURAN</label>
                                <input type="text" class="form-control @error('ukuran') is-invalid @enderror" name="ukuran" value="{{ old('ukuran', $product->seri) }}" placeholder="Masukkan Judul Product">
                            
                                <!-- error message untuk title -->
                                @error('ukuran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">KAMERA DEPAN</label>
                                <input type="text" class="form-control @error('kamera_depan') is-invalid @enderror" name="kamera_depan" value="{{ old('kamera_depan', $product->seri) }}" placeholder="Masukkan Judul Product">
                            
                                <!-- error message untuk title -->
                                @error('kamera_depan')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">KAMERA BELAKANG</label>
                                <input type="text" class="form-control @error('kamera_belakang') is-invalid @enderror" name="kamera_belakang" value="{{ old('kamera_belakang', $product->seri) }}" placeholder="Masukkan Judul Product">
                            
                                <!-- error message untuk title -->
                                @error('kamera_belakang')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">PRICE</label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" placeholder="Masukkan Harga Product">
                                    
                                        <!-- error message untuk price -->
                                        @error('price')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">STOCK</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="Masukkan Stock Product">
                                    
                                        <!-- error message untuk stock -->
                                        @error('stock')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>

                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
</body>
</html>