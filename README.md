# PHMoney

When building run in directory "stubs/public/js/phmoney_assets"

```
./build.sh
```

Add in your web.php entry:
```
Route::get('/phmoney', function () {
        return view('phmoney');
    })->name('phmoney');
    ```
