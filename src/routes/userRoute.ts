import express from 'express';
import AuthorizationUtil from "../utils/AuthorizationUtil.js";

const router = express.Router();

router.get("/profile/:userId?", AuthorizationUtil.authRole("user"), (req, res) => {
    res.render('profile.ejs', {
        show: (req.params.userId == undefined),
        user: {
            name: "Robin Mager",
            company: "MediaINN",
            points: 50
        },
        badges:[
            {
                completed: true,
                info: {
                    title: "",
                    description: "",
                    img: "battle/be_on_time.png"
                }
            },
            {
                completed: false,
                info: {
                    title: "",
                    description: "",
                    img: "battle/thief.png"
                }
            },
            {
                completed: false,
                info: {
                    title: "",
                    description: "",
                    img: "battle/the_best.png"
                }
            },
        ]
    });
})

router.get("/settings")

export default router;