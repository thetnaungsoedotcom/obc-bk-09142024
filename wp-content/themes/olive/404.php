<?php get_header(); ?>
<style>
.pg-not-found-wrap {
    padding-top: 200px;
    padding-bottom: 100px;
    text-align: center;
}
.error-no {
    font-size: 150px;
    line-height: 1;
}
.bk-home-btn {
    margin-top: 30px;
}
.bk-home-btn a {
    color: #000;
}
.not-found-txt span {
    display: inline-block;
    position: relative;
    background: #fff;
    padding-left: 16px;
    padding-right: 16px;
}
.not-found-txt span::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 45%;
    height: 1px;
    background: red;
    transform: translateY(-50%);
}
.not-found-txt span::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 0;
    width: 45%;
    height: 1px;
    background: red;
    transform: translateY(-50%);
}
</style>
<section class="pg-not-found-wrap">
    <div class="container">
        <div>
            <h3 class="error-no"><span>404</span></h3>
            <p class="not-found-txt"><span>PAGE NOT FOUND</span></p>
            <p class="bk-home-btn btn btn-primary"><a href="<?php echo site_url(); ?>" title="<?php bloginfo('name'); ?>">Back Home</a></p>
        </div>
    </div>
</section>
<?php get_footer(); ?>
