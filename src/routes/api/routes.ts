import express from 'express';
import AuthorizationUtil from "../../utils/AuthorizationUtil.js";

const router = express.Router();

router.post("/getBadges", AuthorizationUtil.authApi, (req, res) => {
const {page, size, userid} = req.body;
const userService = new UserService();
const badges = userService.getBadges(userid, page, size);


});


export default router;