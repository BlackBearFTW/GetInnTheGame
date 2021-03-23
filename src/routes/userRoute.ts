import express from 'express';
import AuthorizationUtil from "../utils/AuthorizationUtil.js";
const router = express.Router();

router.get("/profile/:userId?", AuthorizationUtil.authRole("user"), (req, res) => {
    res.render('profile.ejs', {
        user: {
            name: "Robin Mager",
            company: "MediaINN",
            points: 50
        },
        show: (req.params.userId == undefined)
    });
})

router.get("/settings")

export default router;