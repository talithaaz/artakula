{{-- layouts/table.blade.php --}}
<div class="table-wrapper">
    <div class="table-responsive table-artakula">
        <table class="table">
            <thead>
                <tr>
                    @yield('table-head')
                </tr>
            </thead>
            <tbody>
                @yield('table-body')
            </tbody>
        </table>
    </div>
</div>
