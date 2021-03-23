import express from 'express';
import AuthorizationUtil from "../utils/AuthorizationUtil.js";
const router = express.Router();


router.get("/challenges", AuthorizationUtil.authRole("user") ,(req, res) => {

});

router.get("/explanation/:challengeId", AuthorizationUtil.authRole("user"), (req, res) => {

});

router.get("/play/:challengeId", AuthorizationUtil.authRole("user"), (req, res) => {

});

router.get("/completed", AuthorizationUtil.authRole("user"), (req, res) => {

});

export default router;