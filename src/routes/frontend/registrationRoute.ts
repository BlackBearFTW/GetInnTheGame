import express from 'express';
import AuthorizationUtil from "../../utils/AuthorizationUtil.js";
import CompanyRepository from "../../repositories/CompanyRepository.js";
import UserRepository from "../../repositories/UserRepository.js";
import UserEntity from "../../entities/UserEntity.js";

const router = express.Router();

const userRepository = UserRepository.getInstance();
const companyRepository = CompanyRepository.getInstance();

router.route("/login")
    .get(AuthorizationUtil.authRole("visitor"), (req, res) => {
        res.render('frontpage.ejs', {
            title: 'login',
            page: 'login',
        });
    })
    .post((req, res) => {
        req.session.loggedIn = true;
        req.session.email = req.body.email;
        res.redirect("/profile");
    });

router.get("/logout", AuthorizationUtil.authRole("user"), (req, res) => {
    req.session.loggedIn = false;
    res.redirect("/login");
});


router.route("/register/:code")
    .get(AuthorizationUtil.authRole("visitor"), async (req, res) => {
        const company = await companyRepository.getCompanyByToken(req.params.code);

        if (company == null) return res.redirect("/login");

        res.render('frontpage.ejs', {
            title: 'registration',
            page: 'register',
            company: company.NAME,
        });
    })
    .post(AuthorizationUtil.authRole("visitor"), async (req, res) => {

        let user = await userRepository.getUserByEmail(req.body.email);

        //const company = await companyRepository.getCompanyByName(req.body.company);

        if (user != null) return res.redirect("/404");
        if (req.body.password != req.body.confirmpassword) return;


        user = new UserEntity();
        user.EMAIL = req.body.email;
        user.FIRSTNAME = req.body.firstname;
        user.LASTNAME = req.body.lastname;
        user.COMPANY_ID = 1;
        user.QR = await AuthorizationUtil.generateUniqueString();
        user.PASSWORD = await AuthorizationUtil.generateHash(req.body.password);

        //await userRepository.create(user);
        return res.redirect("/login");
    });


router.get("/forgot-password", AuthorizationUtil.authRole("visitor"), (req, res) => {
    res.render('frontpage.ejs', {
        title: 'forgot password',
        page: 'forgot-password',
    });
});


export default router;