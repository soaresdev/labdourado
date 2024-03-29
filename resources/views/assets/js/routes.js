export default [
    { path: '/', redirect: '/home' },
    {
        path: '/home',
        name: 'home',
        component: require('../../screens/Home').default,
    },
    {
        path: '/users',
        name: 'users.index',
        component: require('../../screens/Users/IndexUser').default,
    },
    {
        path: '/providers',
        name: 'providers.index',
        component: require('../../screens/Providers/IndexProvider').default,
    },
    {
        path: '/patients',
        name: 'patients.index',
        component: require('../../screens/Patients/IndexPatient').default,
    },
    {
        path: '/doctors',
        name: 'doctors.index',
        component: require('../../screens/Doctors/IndexDoctor').default,
    },
    {
        path: '/operators',
        name: 'operators.index',
        component: require('../../screens/Operators/IndexOperator').default,
    },
    {
        path: '/procedures',
        name: 'procedures.index',
        component: require('../../screens/Procedures/IndexProcedure').default,
    },
    {
        path: '/lots',
        name: 'lots.index',
        component: require('../../screens/Lots/IndexLot').default,
    },
    {
        path: '/guides-sadt',
        name: 'guides-sadt.index',
        component: require('../../screens/GuidesSadt/IndexGuideSadt').default,
    },
    {
        path: '/guides-sadt/create',
        name: 'guides-sadt.create',
        component: require('../../screens/GuidesSadt/CreateGuideSadt').default,
    },
    {
        path: '/guides-sadt/:id/edit',
        name: 'guides-sadt.edit',
        component: require('../../screens/GuidesSadt/CreateGuideSadt').default,
    },
    {
        path: '*',
        name: 'catch-all',
        redirect: '/home',
    },
]
